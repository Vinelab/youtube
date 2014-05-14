<?php namespace Vinelab\Youtube\Contracts;

interface YoutubeInterface {

    /**
     * return a single video info
     * @param  string $url 
     * @return Vinelab\Youtube\Video      
     */
    public function video($url);

    /**
     * return a channel with its videos
     * @param  string $url       
     * @param  date $synced_at 
     * @return Vinelab\Youtube\Channel            
     */
    public function channel($url, $synced_at=null);

    /**
     * sync the resource
     * @param  Vinelab\Youtube\Video|Vinelab\Youtube\Channel $resource 
     * @return Vinelab\Youtube\Video|Vinelab\Youtube\Channel           
     */
    public function sync($resource);
}