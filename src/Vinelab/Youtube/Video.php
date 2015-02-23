<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\Contracts\VideoInterface;

class Video implements VideoInterface, ResourceInterface {

    /**
     * if the response does not sync_enabled value will take this default value
     */
    const DEFAULT_SYNC_STATUS = false;

    /**
     * response kind for search result
     */
    const SEARCH_RESULT = 'youtube#searchResult';

    /**
     * youtube URL for video
     */
    const URL_VIDEO = 'https://www.youtube.com/watch?v=';

    /**
     * instantiate instances of this class
     *
     * @param  stdClasses|array of stdClasses $responses
     *
     * @return Vinelab\Youtube\Video
     */
    public function make($responses)
    {
        if (! is_array($responses)) { return $this->build($responses); }

        // if array build each response
        return array_map(function ($response) { return $this->build($response); }, $responses);
    }

    /**
     * create, fill and return the object
     *
     * @param $response
     *
     * @return static
     */
    private function build($response)
    {
        $video = new static();
        if ($response->kind == self::SEARCH_RESULT) {
            //use this if the reponse is coming from a search request.
            $video->fillFromSearch((array) $response);
        } else {
            //use this if the response if coming form a single video request.
            $video->fill((array) $response);
        }
         return $video;
    }

    /**
     * fill the data array with the data related to the video
     * @param array $data
     */
    public function fill($data)
    {
        $this->kind         = $data['kind'];
        $this->id           = $data['id'];
        $this->etag         = $data['etag'];
        $this->sync_enabled = (bool) isset($data['sync_enabled']) ? $data['sync_enabled'] : self::DEFAULT_SYNC_STATUS ;
        $this->synced_at    = date("Y-m-d\TH:i:sP");
        $this->snippet      = (array) $data['snippet'];
        $this->thumbnails   = $this->thumbnails($data['snippet']->thumbnails);
        $this->url          = $this->url($data['id']);

        //remove the thumbnails, categoryId and liveBroadcastContent form the response
        unset($this->snippet['thumbnails']);
        unset($this->snippet['categoryId']);
        unset($this->snippet['liveBroadcastContent']);
    }

    /**
     * fill the data array with the data related to the video
     * coming from a search request
     * @param array $data
     */
    public function fillFromSearch($data)
    {
        $this->kind         = $data['id']->kind;
        $this->id           = $data['id']->videoId;
        $this->etag         = $data['etag'];
        $this->sync_enabled = true;
        $this->synced_at    = date("Y-m-d\TH:i:sP");
        $this->snippet      = (array) $data['snippet'];
        $this->thumbnails   = $this->thumbnails($data['snippet']->thumbnails);
        $this->url          = $this->url($data['id']->videoId);

        unset($this->snippet['thumbnails']);
        unset($this->snippet['liveBroadcastContent']);
    }

    /**
     * the video url
     * @return string
     */
    public function url($id = null)
    {
        return self::URL_VIDEO . (is_null($id) ? $this->id : $id);
    }

    /**
     * return an associative array of the thumbnails.
     * @param  stdClass $thumbnails
     * @return array
     */
    public function thumbnails($thumbnails)
    {
        $thumbs = [];
        foreach ($thumbnails as $key => $thumbnail) {
            $thumbs[$key] = $thumbnail->url;
        }

        return $thumbs;
    }

    /**
     * Return the Video data
     * @return array
     */
    public function getYoutubeInfo()
    {
        return (array) $this;
    }

    /**
     * return the synced at date
     * @return date
     */
    public function youtubeSyncedAt()
    {
        return $this->synced_at;
    }

    /**
     * return the published at video date
     * @return date
     */
    public function youtubePublishedAt()
    {
        return $this->snippet['publishedAt'];
    }
}
