<?php namespace Vinelab\Youtube\Contracts;

interface ApiInterface {

    /**
     * Make the api call
     * @param  string $url    
     * @param  array $params 
     * @return stdClass         
     */
    public function get($url, $params);

    /**
     * Get single video info
     * @param  string $video_id 
     * @return Vinelab\Youtube\YoutubeVideo
     */
    public function video($video_id);

    /** 
     * return the channel info by id or by name
     * @param  string $id_or_name 
     * @param  date $synced_at 
     * @return Vinelab\Youtube\Channel
     */
    public function channel($id_or_name, $synced_at=null);

    /**
     * get the channel by ID
     * @param  string $username 
     * @return stdClass           
     */
    public function getChannelById($id);

    /**
     * get the channel by username
     * @param  string $username 
     * @return stdClass           
     */
    public function getChannelByName($username);

    /**
     * get all channel's videos by channel id
     * @param  string $channel_id 
     * @param date $published_after RFC 3339 formatted date-time value (1970-01-01T00:00:00Z)
     * @return array
     */
    public function searchChannelVideos($channel_id, $published_after=null);

    /**
     * get all videos related to a channel
     * @param  string  $channel_id 
     * @param  string  $page       
     * @param  string  $q          
     * @param  integer $max_result 
     * @param  string  $order
     * @param  date $published_after (format: "Y-m-d\TH:i:sP" RFC 3339)
     * @return stdClass              
     */
    public function searchChannelVideosForPage($channel_id, $page=null, $q=null, $max_result=20, $order='date', $published_after=null);
}