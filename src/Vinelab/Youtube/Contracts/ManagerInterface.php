<?php

namespace Vinelab\Youtube\Contracts;

use Vinelab\Youtube\ResourceInterface;

interface ManagerInterface
{
    /**
     * Return a videos info.
     *
     * @param string|array $urls
     *
     * @return \Vinelab\Youtube\Contracts\Vinelab\Youtube\YoutubeVideo
     */
    public function videos($vid);

    /**
     * return the channel's videos by id or by username.
     *
     * @param string $id_or_name
     * @param date   $synced_at
     *
     * @return Vinelab\Youtube\Channel
     */
    public function videosForChannel($id_or_name, $synced_at = null);

    /**
     * Sync a resource (channel or video).
     *
     * @param ResourceInterface $resource
     *
     * @return Channel|Video
     */
    public function sync(ResourceInterface $resource);

    /**
     * add http to the url if it does not exist.
     *
     * @param $url
     *
     * @return string
     */
    public function prepareUrl($url);
}
