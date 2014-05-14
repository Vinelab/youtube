<?php namespace Vinelab\Youtube\Contracts;

interface ManagerInterface {

    /**
     * Return a video info
     * @param  string $vid
     * @return Vinelab\Youtube\Video
     */
    public function video($vid);
    
    /**
     * return the channel's videos by id or by username.
     * @param  string $id_or_name 
     * @param  date $synced_at 
     * @return Vinelab\Youtube\Channel             
     */
    public function videosForChannel($id_or_name, $synced_at=null);

    /**
     * Sync a resource (channel or video)
     * @param  Channel|Video $resource 
     * @return Channel|Video           
     */
    public function sync($resource);
}