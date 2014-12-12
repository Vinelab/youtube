<?php namespace Vinelab\Youtube\Facades;

use Illuminate\Support\Facades\Facade;

class Youtube extends Facade {

    public static function getFacadeAccessor()
    {
        return 'Vinelab\Youtube\Contracts\YoutubeInterface';
    }
}
