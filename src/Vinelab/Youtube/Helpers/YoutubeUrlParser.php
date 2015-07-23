<?php

namespace Vinelab\Youtube\Helpers;

/*
 * @author Adib
 * @author Mahmoud Zalt <mahmoud@vinelab.com>
 */

/*
 * This Helper class is used to parse the youtube URL in all its forms
 * and return the video ids, channel username or channel id.
 */

use Vinelab\Youtube\Exceptions\InvalidVideoUrlException;

class YoutubeUrlParser
{
    /**
     * Parse a youtube URL to get the youtube Vid.
     * Support both full URL (www.youtube.com) and short URL (youtu.be).
     *
     * @param string $youtube_url
     *
     * @return string Video Id
     */
    public static function parseId($youtube_url)
    {
        if (strpos($youtube_url, 'youtube.com')) {
            return self::parseUrlQuery($youtube_url)['v'];
        } elseif (strpos($youtube_url, 'youtu.be')) {
            $path = parse_url($youtube_url)['path'];

            return substr($path, 1);
        }

        throw new InvalidVideoUrlException();
    }

    /**
     * Get the channel reference by supplying the URL of the channel page.
     *
     * @param string $youtube_url
     *
     * @return string channel
     */
    public static function parseChannelUrl($youtube_url)
    {
        $path = parse_url($youtube_url)['path'];

        if (strpos($path, '/channel') === 0 or strpos($path, '/user') === 0) {
            $segments = explode('/', $path);

            return $segments[count($segments) - 1];
        }

        throw new InvalidVideoUrlException();
    }

    /**
     * Get the playlist reference by supplying the URL of the playlist page.
     *
     * @param string $youtube_url
     *
     * @return string playlist
     */
    public static function parsePlaylistUrl($youtube_url)
    {
        $query = parse_url($youtube_url)['query'];

        $get_array = [];
        parse_str($query, $get_array);

        if (isset($get_array['list']) && !empty($get_array['list'])) {
            return $get_array['list'];
        }

        throw new InvalidVideoUrlException();
    }

    /**
     * parse the input url string and return an array of query params.
     *
     * @param string $url the URL
     *
     * @return array
     */
    private static function parseUrlQuery($url)
    {
        $params = [];
        $parsed_url = (isset(parse_url($url)['query'])) ? parse_url($url)['query'] : null;

        if ($parsed_url == null) {
            throw new InvalidVideoUrlException();
        }

        $url_parts = explode('&', $parsed_url);
        foreach ($url_parts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }

        return $params;
    }
}
