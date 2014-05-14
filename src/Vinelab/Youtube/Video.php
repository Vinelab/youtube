<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\Contracts\VideoInterface;

class Video implements VideoInterface {

    /**
     * istantiate an instance of this class
     * @param  stdClass $response 
     * @return Vinelab\Youtube\Video           
     */
    public function make( $response )
    {
        $video = new static;

        if($response->kind == "youtube#searchResult")
        {
            //use this if the reponse is coming from a search request.
            $video->fillFromSearch((array)$response);
        } else {
            //use this if the response if coming form a single video request.
            $video->fill( (array)$response );
        }

        return $video;
    }

    /**
     * fill the data array with the data related to the video
     * @param  array $data 
     */
    public function fill( $data )
    {
        $this->kind         = $data['kind'];
        $this->id           = $data['id'];
        $this->etag         = $data['etag'];
        $this->sync_enabled = true;
        $this->synced_at    = date("Y-m-d\TH:i:sP");
        $this->snippet      = (array)$data['snippet'];
        $this->thumbnails   = $this->thumbnails($data['snippet']->thumbnails);

        //remove the thumbnails, categoryId and liveBroadcastContent form the response
        unset($this->snippet['thumbnails']);
        unset($this->snippet['categoryId']);
        unset($this->snippet['liveBroadcastContent']);
    }

    /**
     * fill the data array with the data related to the video
     * coming from a search request
     * @param  array $data 
     */
    public function fillFromSearch( $data )
    {
        $this->kind         = $data['id']->kind;
        $this->id           = $data['id']->videoId;
        $this->etag         = $data['etag'];
        $this->sync_enabled = true;
        $this->synced_at    = date("Y-m-d\TH:i:sP");
        $this->snippet      = (array)$data['snippet'];
        $this->thumbnails   = $this->thumbnails($data['snippet']->thumbnails);

        unset($this->snippet['thumbnails']);
        unset($this->snippet['liveBroadcastContent']);
    }

    /**
     * the video url
     * @return string
     */
    public function url()
    {
        return 'https://www.youtube.com/watch?v='.$this->id;
    }

    /**
     * return an associative array of the thumbnails.
     * @param  stdClass $thumbnails 
     * @return array             
     */
    public function thumbnails($thumbnails)
    {
        $thumbs = [];
        foreach($thumbnails as $key=>$thumbnail)
        {
            $thumbs[$key] = $thumbnail->url;
        }

        return $thumbs;
    }
}