<?php

namespace Vinelab\Youtube;

/*
 * @author Adib
 * @author Mahmoud Zalt <mahmoud@vinelab.com>
 */

use Vinelab\Youtube\Contracts\ManagerInterface;
use Vinelab\Youtube\Contracts\YoutubeInterface;

class Youtube implements YoutubeInterface
{
    /**
     * The manager instance.
     *
     * @var Vinelab\Youtube\Contracts\ManagerInterface
     */
    protected $manager;

    /**
     * Create a new instance of Youtube.
     *
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * return a videos info.
     *
     * @param array $urls
     *
     * @return Vinelab\Youtube\Video
     */
    public function videos($urls)
    {
        return $this->manager->videos($urls);
    }

    /**
     * return a single video info.
     *
     * @param string $url
     *
     * @return Vinelab\Youtube\Video
     */
    public function video($url)
    {
        return $this->videos($url);
    }

    /**
     * return a channel with its videos.
     *
     * @param string $url
     * @param date   $synced_at
     *
     * @return Vinelab\Youtube\Channel
     */
    public function channel($url, $synced_at = null)
    {
        return $this->manager->videosForChannel($url, $synced_at);
    }

    /**
     * return a playlist with its videos.
     *
     * @param string $url
     * @param date   $synced_at
     *
     * @return Vinelab\Youtube\Playlist
     */
    public function playlist($url, $synced_at = null)
    {
        return $this->manager->videosForPlaylist($url, $synced_at);
    }

    /**
     * sync the resource.
     *
     * @param Video|Channel $resource
     *
     * @return Video|Channel
     */
    public function sync(ResourceInterface $resource)
    {
        return $this->manager->sync($resource);
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
        return $this->manager->prepareUrl($url);
    }
}
