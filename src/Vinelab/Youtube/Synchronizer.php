<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\VideoCollection;
use Vinelab\Youtube\ResourceInterface;
use Vinelab\Youtube\Contracts\ApiInterface;
use Vinelab\Youtube\Contracts\ChannelInterface as YoutubeChannelInterface;
use Vinelab\Youtube\Contracts\VideoManagerInterface;
use Vinelab\Youtube\Contracts\SynchronizerInterface;
use Vinelab\Youtube\Contracts\YoutubeParserInterface;
use Vinelab\Youtube\Exceptions\IncompatibleParametersException;

class Synchronizer implements SynchronizerInterface {

    /**
     * The api instance.
     * @var Vinelab\Youtube\Contracts\ApiInterface
     */
    protected $api;

    /**
     * $the ChannelInterface instance
     * @var Vinelab\Youtube\Contracts\ChannelInterface
     */
    protected $channel;

    /**
     * $data will store all the data
     * after we sync the channels and
     * videos.
     * @var array
     */
    protected $data = [];

    /**
     * Create a new instance of the VideoSynchroniser
     * @param VideoManagerInterface $manager
     */
    public function __construct(ApiInterface $api, YoutubeChannelInterface $channel)
    {
        $this->api = $api;
        $this->channel  = $channel;
    }

    /**
     * Sync the resources.
     *
     * in case a resource(video, or channel) has been been deleted
     * a 'IncompatibleParametersException' will be thrown
     * which means that the existing data and the new one are not
     * compatible(different kind) because if the resource has been deleted
     * Null will be returned in the response.
     *
     * @param  ResourceInterface $existing_data
     * @return Channel|Video
     */
    public function sync($resource)
    {
        $url = $resource->url();

        // sync channels: Vinelab\Youtube\Channel
        if($this->typeOf($resource) == 'Najem\Artists\Channel')
        {
            $synced_at = new \DateTime($resource->synced_at);
            $synced_at = $synced_at->format('Y-m-d\TH:i:sP');

            $response = $this->api->channel($resource->channel_id, $synced_at);
            if(count($response->items) == 0)
            {
                $response = $this->api->channel($resource->channel_id);
            }
            //check if sync is enabled for a channel
            if($this->syncable($resource))
            {
                //Sync the channel with the new data
                $this->setChannelData($response);
            }
            //sync the video if changed
            $this->syncVideos($resource, $response);

            // sync single videos: Vinelab\Youtube\Video
        } else if($this->typeOf($resource) == 'Najem\Artists\Video')
        {
            $response = $this->api->video($url);
            //check if sync if enabled for a video
            if($this->syncable($resource))
            {
                //check if the etags are not the same.
                //if so, set the data to be equal to the
                //reponse value and return true.
                if($this->videoDiff($resource, $response))
                {
                    $this->data = $response;
                }
            }
        } else {
            //this will be throw if the following conditions were satisfied:
            //1. video + channel has been passed to the Sync method.
            //2. two videos has been passed with one of them deleted.
            //notice that we will never have a condition where an existing resource's 
            //value is null, because it will mean that the actual resource doesn't exist.
            throw new IncompatibleParametersException;
        }

        return $this->data;
    }

    /**
     * check if the video etags are different.
     * @param  Vinelab\Youtube\Video $resource
     * @param  Vinelab\Youtube\Video $response
     * @return Boolean
     */
    protected function videoDiff($resource, $response)
    {
        //if the etag is different and if sync is enabled.
        //then return the new video info.
        if($resource->etag != $response->etag)
        {
            return true;
        }
        //if the etags are the same, this means that 
        //there are no changes in the video. 
        return false;
    }

    /**
     * Sync the channel without the videos
     * @param  Channel $response
     */
    protected function setChannelData($response)
    {
        $this->data = $response;
    }

    /**
     * Sync the video inside the channel
     * @param  Channel $resource
     * @param  Channel $response
     */
    protected function syncVideos($resource, $response)
    {
        $resource_videos = $resource->videos;
        $old_etags = $resource_videos->lists('etag');

        $response_videos = $response->videos;
        $new_etags = $response_videos->lists('etag');

        //check what are the common etags between the resource and response
        $intersect = array_intersect($new_etags, $old_etags);

        //this should be added to the etags array
        $added_etags = array_diff($new_etags, $old_etags);

        //these must not exist in the etags array
        $deleted_etags = array_diff($old_etags, $new_etags);

        //this contains the etags for all the videos that should exist in the final album
        $etags = array_merge($intersect, $added_etags);

        //this will hold all the Video object 
        $videos = new VideoCollection;

        //add all the video from the existing channel (assuming that it should exist in the final channel)
        //to the Video collection (that will contain the final video result)
        foreach($resource->videos as $video)
        {
            //if sync is not enabled don't add the video
            if($this->syncable($video))
            {
                foreach($etags as $key=>$etag)
                {
                    if($video->etag == $etag)
                    {
                        //add the video
                        $videos->push($video);
                        //remove the etag from the final etags array
                        //so that it won't be used when looping in the new channel(newly fetched)
                        unset($etags[$key]);
                    }
                }
            }
        }

        //add any video that still exist and to be added to the final video collection.
        //use the newly fetched channel
        if( ! empty($etags))
        {
            foreach($response->videos as $video)
            {
                //if sync is not enabled don't add the video
                if($this->syncable($video))
                {
                    foreach($etags as $etag)
                    {
                        if($video->etag == $etag)
                        {
                            //add the video
                            $videos->push($video);
                        }
                    }
                }
            }
        }
        //set the video attribute to be the newly created video collection.
        $this->data->setVideos($videos);
    }

    /**
     * return the value of sync_enabled
     * @param  Channel|Video $data
     * @return boolean
     */
    protected function syncable($data)
    {
        return $data->sync_enabled;
    }

    /**
     * return the type of object
     * @param  Object $object
     * @return string
     */
    protected function typeOf($object)
    {
        return (isset($object)) ? get_class($object) : null;
    }

}
