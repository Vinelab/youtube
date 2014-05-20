<?php namespace Vinelab\Youtube;

use HttpClient;
use Vinelab\Http\Client;
use Vinelab\Youtube\VideoCollection;
use Illuminate\Config\Repository as Config;
use Vinelab\Youtube\Contracts\ApiInterface;
use Vinelab\Youtube\Contracts\VideoInterface as YoutubeVideoInterface;
use Vinelab\Youtube\Contracts\ParserInterface;
use Vinelab\Youtube\Validators\VideoResponseValidator;
use Vinelab\Youtube\Validators\ChannelResponseValidator;
use Vinelab\Youtube\Validators\SearchResponseValidator;

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

    protected $http_client;

    /**
     * Initialize the Youtube instance
     * @param Config $config
     */
    public function __construct(Config $config,
                                Client $http_client,
                                YoutubeVideoInterface $video,
                                ParserInterface $parser,
                                VideoResponseValidator $video_validator,
                                ChannelResponseValidator $channel_validator,
                                SearchResponseValidator $search_validator)
    {
        $this->config   = $config;
        $this->http_client = $http_client;
        $configuration  = $this->config->get('Vinelab\Youtube::youtube');

        $this->key      = $configuration['key'];
        $this->uris     = $configuration['uri'];

        $this->video = $video;
        $this->parser = $parser;
        $this->video_validator = $video_validator;
        $this->search_validator = $search_validator;
        $this->channel_validator = $channel_validator;
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
        $response = $this->get($api_url, $params);
        //validate if the youtube response satisfy what is expected.
        $this->video_validator->validate($response);
        //check if the video hasn't been deleted, then return the result accordingly.
        //$response->items will always exist in the response. however, if the video 
        //has been deleted, items would be empty. So, it would be valid to check if 
        //it's empty before returning the result.
        return (! empty($response->items)) ? $this->video->make(array_pop($response->items)) : null;
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

        //if we have videos, loop through them and validate them one by one.
        foreach($pages as $page)
        {
            foreach($page->items as $video)
            {
                //validate the videos
                $this->search_validator->validate($video);
            }
        }

        return $pages;
    }

    public function channel($id_or_name, $synced_at=null)
    {
        $channel = $this->getChannelById($id_or_name);

        if($this->isEmpty($channel->items))
        {
            $channel = $this->getChannelByName($id_or_name);
        }
        //validate the channel info
        $this->channel_validator->validate($channel);

        $channel_id = $channel->items[0]->id;
        //get the channel videos
        $video_pages = $this->searchChannelVideos($channel_id, $synced_at);

        $parsed_videos = $this->parser->parse($video_pages, $channel);

        return $parsed_videos;
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
     * check if items is empty
     * @param  array  $items
     * @return boolean
     */
    protected function isEmpty($items)
    {
        return empty($items);
    }

    /**
     * Make the api call
     * @param  string $url
     * @param  array $params
     * @return stdClass
     */
    public function get($url, $params)
    {
        $result = $this->http_client->get(compact('url', 'params'));
        return $result->json();
    }
}
