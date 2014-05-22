<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\VideoCollection;
use Vinelab\Youtube\ResourceInterface;
use Vinelab\Youtube\Contracts\ChannelInterface;

class Channel implements ChannelInterface, ResourceInterface {

    /**
     * The Channel Data
     * @var array
     */
    protected $data = [];

    /**
     * Istantiate an instance of this class.
     * @param  stdClass $channel_info 
     * @param  Vinelab\Youtube\VideoCollection $videos       
     * @return Vinelab\Youtube\Channel               
     */
    public function make($channel_info, VideoCollection $videos)
    {
        $channel = new static;

        $channel->fill( (array)$channel_info, $videos );
        
        return $channel;
    }

    /**
     * fill the Channel object with its data
     * @param  array $channel_info 
     * @param  Vinelab\Youtube\VideoCollection $videos       
     */
    protected function fill( array $channel_info, VideoCollection $videos )
    {
        $items = (array)$channel_info['items'][0];
        $kind             =   $items['kind'];
        $etag             =   $items['etag'];
        $sync_enabled     =   true;
        $id               =   $items['id'];
        $synced_at        =   date("Y-m-d\TH:i:sP");

        $snippet        =   (array)$items['snippet'];
        $title          =   $snippet['title'];
        $description    =   $snippet['description'];
        $published_at   =   $snippet['publishedAt'];

        $thumbnails         =   $this->thumbnails($items['snippet']->thumbnails);
        $default_thumb      =   $thumbnails['default'];
        $medium_thumb       =   $thumbnails['medium'];
        $high_thumb         =   $thumbnails['high'];

        $content_details        =   $this->contentDetails((array)$items['contentDetails']);
        $playlist_likes         =   $content_details['relatedPlaylists']['likes'];
        $playlist_uploads       =   $content_details['relatedPlaylists']['uploads'];
        $google_plus_user_id    =   $content_details['googlePlusUserId'];

        unset($snippet['thumbnails']);

        // similar to $this->data = compact('kind'...)
        $this->setData(  
                    $kind, 
                    $etag, 
                    $sync_enabled, 
                    $id, 
                    $synced_at,
                    $title, 
                    $description, 
                    $published_at, 
                    $default_thumb, 
                    $medium_thumb, 
                    $high_thumb, 
                    $playlist_likes, 
                    $playlist_uploads, 
                    $google_plus_user_id, 
                    $videos
                );
    }

    /**
     * setData the data 
     * @param  string $kind                
     * @param  stringt $etag                
     * @param  boolean $sync_enabled        
     * @param  string $id    
     * @param  string $synced_at              
     * @param  string $title               
     * @param  string $description         
     * @param  string $published_at        
     * @param  string $default_thumb       
     * @param  string $medium_thumb        
     * @param  string $high_thumb          
     * @param  string $playlist_likes      
     * @param  string $playlist_uploads    
     * @param  string $google_plus_user_id 
     * @param  string $videos              
     * @return array                      
     */
    public function setData($kind, 
                            $etag, 
                            $sync_enabled, 
                            $id,
                            $synced_at, 
                            $title, 
                            $description, 
                            $published_at, 
                            $default_thumb, 
                            $medium_thumb, 
                            $high_thumb, 
                            $playlist_likes, 
                            $playlist_uploads, 
                            $google_plus_user_id, 
                            $videos)
    {
        $this->data = compact('kind', 
                      'etag', 
                      'sync_enabled',
                      'id',  
                      'synced_at',
                      'title', 
                      'description', 
                      'published_at', 
                      'default_thumb', 
                      'medium_thumb', 
                      'high_thumb', 
                      'playlist_likes', 
                      'playlist_uploads', 
                      'google_plus_user_id', 
                      'videos');
    }

    /**
     * the channel url
     * @return string
     */
    public function url()
    {
        return 'https://www.youtube.com/channel/'.$this->id;
    }

    /**
     * return an associative array of the thumbnails
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

    /**
     * return an associative array of the content details
     * @param  array $content_details 
     * @return array                  
     */
    public function contentDetails($content_details)
    {
        $result = [];
        $result['relatedPlaylists'] = (array)$content_details['relatedPlaylists'];
        $result['googlePlusUserId'] = $content_details['googlePlusUserId'];

        return $result;
    }

    /**
     * set the video
     * @param VideoCollection $video_collection 
     */
    public function setVideos($video_collection)
    {
        $this->data['videos'] = $video_collection;
    }

    /**
     * magic method used to access the protected 
     * Channel data.
     * @param  string $name 
     * @return string       
     */
    public function __get($name)
    {
        return  ( isset($this->data[$name]) ) ? $this->data[$name] : null;
    }

    /**
     * Return the Channel data
     * @return array 
     */
    public function getYoutubeInfo()
    {
        return $this->data;
    }

    /**
     * return the channel id
     * @return integer 
     */
    public function id()
    {
        return $this->data['id'];
    }

    public function youtubeSyncedAt()
    {
        return $this->data['synced_at'];
    }
}