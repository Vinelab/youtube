<?php

namespace Vinelab\Youtube\Contracts;

use Vinelab\Youtube\VideoCollection;

interface ChannelInterface
{
    /**
     * Istantiate an instance of this class.
     *
     * @param stdClass                        $channel_info
     * @param Vinelab\Youtube\VideoCollection $videos
     *
     * @return Vinelab\Youtube\Channel
     */
    public function make($channel_info, VideoCollection $videos);
}
