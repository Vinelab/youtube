<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\VideoCollection;
use Vinelab\Youtube\Contracts\VideoInterface;
use Vinelab\Youtube\Contracts\ApiInterface;
use Vinelab\Youtube\Contracts\ManagerInterface;
use Vinelab\Youtube\Contracts\ParserInterface;
use Vinelab\Youtube\Validators\VideoResponseValidator;
use Vinelab\Youtube\Validators\ChannelResponseValidator;
use Vinelab\Youtube\Validators\SearchResponseValidator;
use Vinelab\Youtube\Contracts\SynchronizerInterface;
use Vinelab\Youtube\Helpers\YoutubeUrlParser as UrlParser;

class Manager implements ManagerInterface {

    /**
     * The api instance.
     * @var Vinelab\Youtube\Contracts\ApiInterface
     */
    protected $api;

    /**
     * The video instance.
     * @var Vinelab\Youtube\Contracts\VideoInterface
     */
    protected $video;

    /**
     * The parser instance
     * @var Vinelab\Youtube\Contracts\ParserInterface
     */
    protected  $parser;

    /**
     * The synchronizer instance
     * @var Vinelab\Youtube\Contracts\SynchronizerInterface
     */
    protected $synchronizer;

    /**
     * The video validator instance
     * @var Vinelab\Youtube\Validators\VideoResponseValidator
     */
    protected $video_validator;

    /**
     * The channel validator instance
     * @var Vinelab\Youtube\Validators\ChannelResponseValidator
     */
    protected $channel_validator;

    /**
     * The search validator instance
     * @var Vinelab\Youtube\Validators\SearchResponseValidator
     */
    protected $search_validator;

    /**
     * Create a new Manager instance
     * @param ApiInterface $youtube 
     * @param VideoInterface   $video
     * @param ParserInterface $parser 
     * @param VideoResponseValidator $video_validator  
     * @param ChannelResponseValidator $channel_validator 
     * @param SearchResponseValidaotr $search_validator 
     */
    public function __construct(ApiInterface $api, 
                                VideoInterface $video, 
                                ParserInterface $parser,
                                VideoResponseValidator $video_validator,
                                ChannelResponseValidator $channel_validator,
                                SearchResponseValidator $search_validator,
                                SynchronizerInterface $synchronizer)
    {
        $this->api = $api;
        $this->video = $video;
        $this->parser = $parser;
        $this->synchronizer = $synchronizer;
        $this->video_validator = $video_validator;
        $this->search_validator = $search_validator;
        $this->channel_validator = $channel_validator;
    }

    /**
     * Return a video info
     * @param  string $url
     * @return Vinelab\Youtube\Video
     */
    public function video($url)
    { 
        //parser the url and return the video id
        $vid = UrlParser::parseId($url);

        $response =  $this->api->video($vid);

        //validate if the youtube response satisfy what is expected.
        $this->video_validator->validate($response);

        //check if the video hasn't been deleted, then return the result accordingly.
        //$response->items will always exist in the response. however, if the video 
        //has been deleted, items would be empty. So, it would be valid to check if 
        //it's empty before returning the result.
        return ( ! empty($response->items)) ? $this->video->make(array_pop($response->items)) : null;
    }

    /**
     * return the channel's videos by id or by username.
     * @param  string $id_or_name 
     * @param  date $synced_at 
     * @return Vinelab\Youtube\Channel             
     */
    public function videosForChannel($url, $synced_at=null)
    {   
        //parse the url and the return the channel id or name
        $id_or_name = UrlParser::parseChannelUrl($url);

        $channel = $this->channel($id_or_name);

        $channel_id = $channel->items[0]->id;

        $video_pages = $this->api->searchChannelVideos($channel_id, $synced_at);
        
        //check if the video exaists.
        if(isset($video_pages[0]->items)) 
        {   
            //if we have videos, loop through them and validate them one by one.
            foreach($video_pages[0]->items as $video)
            {
                $this->search_validator->validate($video);
            }
        }

        $parsed_videos = $this->parser->parse($video_pages, $channel);

        return $parsed_videos;
    }

    /**
     * Sync a resource (channel or video)
     * @param  Channel|Video $resource 
     * @return Channel|Video           
     */
    public function sync($resource)
    {
        if(is_null($resource))
        {
            return false;
        }

        $url = $resource->url();

        switch ($this->typeOf($resource)) 
        {
            case 'Vinelab\Youtube\Video':
                $response = $this->video($url);
                break;
            
            case 'Vinelab\Youtube\Channel':
                $response = $this->videosForChannel($url, $resource->synced_at);
                if(is_null($response->items))
                {  
                    $response = $this->videosForChannel($url, $resource);
                }
                break;
        }
        
        return $this->synchronizer->sync($resource, $response);
    }

    /**
     * return the channel info by id or by name
     * @param  string $id_or_name 
     * @return stdClass
     */
    protected function channel($id_or_name)
    {
        $channel = $this->api->getChannelById($id_or_name);

        if($this->isEmpty($channel->items))
        {
            $channel = $this->api->getChannelByName($id_or_name);
        }
        //validate the channel info
        $this->channel_validator->validate($channel);

        return $channel;
    }

    /**
     * check if items is empty
     * @param  array  $items 
     * @return boolean       
     */
    protected function isEmpty($items)
    {
        return empty($items);
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