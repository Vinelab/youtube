<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\Contracts\VideoInterface;
use Vinelab\Youtube\Contracts\ChannelInterface;
use Vinelab\Youtube\Contracts\ParserInterface;

class Parser implements ParserInterface {

    /**
     * The videoInterface instance
     * @var Vinelab\Youtube\Contracts\VideoInterface
     */
    protected $video;

    /**
     * Then ChannelInterface instance
     * @var Vinelab\Youtube\Contracts\ChannelInterface
     */
    protected $channel;

    /**
     * Create a new Parser instance
     * @param VideoInterface   $video
     * @param ChannelInterface $channel
     */
    public function __construct(VideoInterface $video, ChannelInterface $channel)
    {
        $this->video = $video;
        $this->channel = $channel;
    }

    /**
     * Parse the given video and channel data.
     * @param  array                   $video_pages
     * @param  array                   $channel
     * @return Vinelab\Youtube\Channel
     */
    public function parse($video_pages, $channel)
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
}
