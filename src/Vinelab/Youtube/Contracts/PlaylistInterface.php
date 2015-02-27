<?php namespace Vinelab\Youtube\Contracts;

use Vinelab\Youtube\VideoCollection;

interface PlaylistInterface {

    /**
     * Istantiate an instance of this class.
     *
     * @param  stdClass                        $playlist_info
     * @param  Vinelab\Youtube\VideoCollection $videos
     *
     * @return Vinelab\Youtube\playlist
     */
    public function make($playlist_info, VideoCollection $videos);
}
