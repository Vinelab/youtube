<?php namespace Vinelab\Youtube\Contracts;

interface ParserInterface {

    /**
     * Parse the given video and channel data.
     * @param  array                           $video_pages
     * @param  array                           $channel
     * @return Vinelab\Youtube\VideoCollection
     */
    public function parse($video_pages, $channel);
}
