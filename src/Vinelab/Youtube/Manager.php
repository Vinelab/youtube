<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\Contracts\ApiInterface;
use Vinelab\Youtube\Contracts\ManagerInterface;
use Vinelab\Youtube\Contracts\SynchronizerInterface;
use Vinelab\Youtube\Helpers\YoutubeUrlParser as UrlParser;

class Manager implements ManagerInterface {

    /**
     * The api instance.
     * @var Vinelab\Youtube\Contracts\ApiInterface
     */
    protected $api;

    /**
     * The synchronizer instance
     * @var Vinelab\Youtube\Contracts\SynchronizerInterface
     */
    protected $synchronizer;

    /**
     * Create a new Manager instance
     * @param ApiInterface          $youtube
     * @param SynchronizerInterface $synchronizer
     */
    public function __construct(ApiInterface $api,
                                SynchronizerInterface $synchronizer)
    {
        $this->api = $api;
        $this->synchronizer = $synchronizer;
    }

    /**
     * Return a video info
     * @param  string                $url
     * @return Vinelab\Youtube\Video
     */
    public function video($url)
    {
        //parser the url and return the video id
        $vid = UrlParser::parseId($url);

        return $this->api->video($vid);
    }

    /**
     * return the channel's videos by id or by username.
     * @param  string                  $id_or_name
     * @param  date                    $synced_at
     * @return Vinelab\Youtube\Channel
     */
    public function videosForChannel($url, $synced_at = null)
    {
        //parse the url and the return the channel id or name
        $id_or_name = UrlParser::parseChannelUrl($url);

        return $this->api->channel($id_or_name, $synced_at);
    }

    /**
     * Sync a resource (channel or video)
     * @param  ResourceInterface $resource
     * @return Channel|Video
     */
    public function sync(ResourceInterface $resource)
    {
        if (is_null($resource)) {
            return false;
        }

        return $this->synchronizer->sync($resource);
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

    /**
     * add http to the url if it does not exist.
     *
     * @param $url
     *
     * @return string
     */
    public function prepareUrl($url)
    {
        if (!preg_match('/http[s]?:\/\//', $url, $matches)) {
            $url = 'http://'.$url;

            return $url;
        }

        return $url;
    }
}
