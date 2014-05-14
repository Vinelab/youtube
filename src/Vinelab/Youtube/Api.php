<?php namespace Vinelab\Youtube;

use HttpClient;
use Illuminate\Config\Repository as Config;
use Vinelab\Youtube\Contracts\ApiInterface;

class Api implements ApiInterface {

    /**
     * The api key
     * @var String
     */
    protected $key;

    /**
     * The api URLs
     * @var Array
     */
    protected $uris = [];

    /**
     * The configuration instance
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Initialize the Youtube instance
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config   = $config;
        $configuration  = $this->config->get('Vinelab\Youtube::youtube');

        $this->key      = $configuration['key'];
        $this->uris     = $configuration['uri'];
    }

    /**
     * Get single video info
     * @param  string $video_id 
     * @return stdClass
     */
    public function video($video_id)
    {
        //set the url used for the api call
        $api_url = $this->uris['videos.list'];

        //set the parameters passed with the api call
        $params = [
                'id'    => $video_id,
                'key'   => $this->key,
                'part'  => 'id, snippet'
            ];

        //make the api call
        return $this->get($api_url, $params); 
    }

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
    public function searchChannelVideosForPage($channel_id, $page=null, $q=null, $max_result=20, $order='date', $published_after=null)
    {
        $api_url = $this->uris['search.list'];
        
        $params = [
            'q'                 =>  $q,
            'type'              =>  'video',
            'channelId'         =>  $channel_id,
            'key'               =>  $this->key,
            'part'              =>  'id, snippet',
            'pageToken'         =>  $page,
            'maxResults'        =>  $max_result,
            'order'             =>  $order,
            'publishedAfter'    =>  $published_after
        ];

        return $this->get($api_url, $params);
    }

    /**
     * check whether the given data has more pages.
     * @param  string  $result 
     * @return boolean         
     */
    protected function hasMorePages($result)
    {
        return isset($result->nextPageToken);
    }

    /**
     * return all channel's videos by channel id
     * @param  string $channel_id 
     * @param date $published_after RFC 3339 formatted date-time value (1970-01-01T00:00:00Z)
     * @return array
     */
    public function searchChannelVideos($channel_id, $published_after=null)
    {
        $pages = [];
        $page_token = null;

        do {
            $res = $this->searchChannelVideosForPage($channel_id, $page_token, $published_after);
            $page_token = ( isset($res->nextPageToken) ) ? $res->nextPageToken : null;
            $has_pages = $this->hasMorePages($res);
            array_push($pages, $res);
        } while($has_pages);

        return $pages;
    }

    /**
     * return the channel by username
     * @param  string $username 
     * @return stdClass           
     */
    public function getChannelByName($username)
    {
        //set the url used for the api call
        $api_url = $this->uris['channels.list'];

        //set the parameters passed with the api call
        $params = [
            'forUsername'   => $username,
            'key'           => $this->key,
            'part'          => 'id,snippet,contentDetails'
        ];

        //make the api call
        return $this->get($api_url, $params);
    }

    /**
     * return the channel by ID
     * @param  string $username 
     * @return stdClass           
     */
    public function getChannelById($id)
    {
        //set the url used for the api call
        $api_url = $this->uris['channels.list'];

        //set the parameters passed with the api call
        $params = [
            'id'        => $id,
            'key'       => $this->key,
            'part'      => 'id,snippet,contentDetails'
        ];

        //make the api call
        return $this->get($api_url, $params);
    }

    /**
     * Make the api call
     * @param  string $url    
     * @param  array $params 
     * @return stdClass         
     */
    public function get($url, $params)
    {
        $result = HttpClient::get(compact('url', 'params'));
        return $result->json();
    }
}
