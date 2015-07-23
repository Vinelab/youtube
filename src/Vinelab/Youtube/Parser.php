<?php

namespace Vinelab\Youtube;

use Vinelab\Youtube\Contracts\PlaylistInterface;
use Vinelab\Youtube\Contracts\VideoInterface;
use Vinelab\Youtube\Contracts\ChannelInterface;
use Vinelab\Youtube\Contracts\ParserInterface;

class Parser implements ParserInterface
{
    /**
     * The videoInterface instance.
     *
     * @var Vinelab\Youtube\Contracts\VideoInterface
     */
    protected $video;

    /**
     * Then ChannelInterface instance.
     *
     * @var Vinelab\Youtube\Contracts\ChannelInterface
     */
    protected $channel;

    /**
     * Create a new Parser instance.
     *
     * @param \Vinelab\Youtube\Contracts\VideoInterface    $video
     * @param \Vinelab\Youtube\Contracts\ChannelInterface  $channel
     * @param \Vinelab\Youtube\Contracts\PlaylistInterface $playlist
     */
    public function __construct(
        VideoInterface $video,
        ChannelInterface $channel,
        PlaylistInterface $playlist
    ) {
        $this->video = $video;
        $this->channel = $channel;
        $this->playlist = $playlist;
    }

    /**
     * Parse the given video and channel pr playlist data.
     *
     * @param array $video_pages
     * @param array $kind
     *
     * @return Vinelab\Youtube\Channel|Vinelab\Youtube\Playlist
     */
    public function parse($video_pages, $kind, $type = 'channel')
    {
        if ($type == 'channel') {
            return $this->parseChannel($video_pages, $kind);
        }
        if ($type == 'playlist') {
            return $this->parsePlaylist($video_pages, $kind);
        }

        return;
    }

    /**
     * Parse the given video and channel data.
     *
     * @param array $video_pages
     * @param array $channel
     *
     * @return Vinelab\Youtube\Channel
     */
    public function parseChannel($video_pages, $channel)
    {
        $videos = new VideoCollection();
        //loop through the pages return from the api call
        //and add all the video to the Video Collection to
        //be passed to the 'channel make method'.
        foreach ($video_pages as $page) {
            foreach ($page->items as $video) {
                $videos->push($this->video->make($video));
            }
        }
        //return a new channel with the new data.
        return $this->channel->make($channel, $videos);
    }

    /**
     * Parse the given video and playlist data.
     *
     * @param array $video_pages
     * @param array $playlist
     *
     * @return Vinelab\Youtube\Playlist
     */
    public function parsePlaylist($video_pages, $playlist)
    {
        $videos = new VideoCollection();
        //loop through the pages return from the api call
        //and add all the video to the Video Collection to
        //be passed to the 'channel make method'.
        foreach ($video_pages as $page) {
            foreach ($page->items as $video) {
                $videos->push($this->video->make($video));
            }
        }
        //return a new channel with the new data.
        return $this->playlist->make($playlist, $videos);
    }
}
