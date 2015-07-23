<?php

namespace Vinelab\Youtube\Tests;

use Mockery as M;
use Vinelab\Youtube\Video;

class VideoTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->video = new Video();
    }

    public function test_make_video_using_fillFromSearch()
    {
        $response = M::mock(new \stdClass());
        $response->kind = 'youtube#searchResult';
        $id = new \stdClass();
        $id->kind = '';
        $id->videoId = '';

        $response->id = $id;
        $response->etag = 'etag';
        $response->sync_enabled = true;
        $response->synced_at = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass();
        $snippet->thumbnails = new \stdClass();

        $response->snippet = $snippet;
        $response->thumbnails = [
                        'default' => [
                            'url' => 'example',
                        ],
                        'medium' => [
                            'url' => 'example',
                        ],
                        'high' => [
                            'url' => 'example',
                        ],
                    ];

        $video_obj = $this->video->make($response);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $video_obj);
    }

    public function test_make_video_using_fill()
    {
        $response = M::mock(new \stdClass());
        $response->kind = 'youtube#video';
        $id = new \stdClass();
        $id->kind = '';
        $id->videoId = '';

        $response->id = $id;
        $response->etag = 'etag';
        $response->sync_enabled = true;
        $response->synced_at = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass();
        $snippet->thumbnails = new \stdClass();

        $response->snippet = $snippet;
        $response->thumbnails = [
                        'default' => [
                            'url' => 'example',
                        ],
                        'medium' => [
                            'url' => 'example',
                        ],
                        'high' => [
                            'url' => 'example',
                        ],
                    ];

        $video_obj = $this->video->make($response);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $video_obj);
    }

    public function test_url()
    {
        $response = M::mock(new \stdClass());
        $response->kind = 'youtube#video';
        $response->id = 'KKNxOTJES1U';
        $response->etag = 'etag';
        $response->sync_enabled = true;
        $response->synced_at = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass();
        $snippet->thumbnails = new \stdClass();

        $response->snippet = $snippet;
        $response->thumbnails = [
                        'default' => [
                            'url' => 'example',
                        ],
                        'medium' => [
                            'url' => 'example',
                        ],
                        'high' => [
                            'url' => 'example',
                        ],
                    ];

        $video_obj = $this->video->make($response);
        $url = $video_obj->url();
        $expected = 'https://www.youtube.com/watch?v=KKNxOTJES1U';
        $this->assertEquals($url, $expected);
    }

    public function test_returning_the_thumbnails()
    {
        $thumbnail = M::mock(new \stdClass());

        $default = M::mock(new \stdClass());
        $default->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/default.jpg';
        $default->width = 120;
        $default->height = 90;

        $medium = M::mock(new \stdClass());
        $medium->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/mqdefault.jpg';
        $medium->width = 320;
        $medium->height = 180;

        $high = M::mock(new \stdClass());
        $high->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/hqdefault.jpg';
        $high->width = 480;
        $high->height = 360;

        $thumbnail->default = $default;
        $thumbnail->medium = $medium;
        $thumbnail->high = $high;

        $expected = [
          'default' => 'https://i1.ytimg.com/vi/KKNxOTJES1U/default.jpg',
          'medium' => 'https://i1.ytimg.com/vi/KKNxOTJES1U/mqdefault.jpg',
          'high' => 'https://i1.ytimg.com/vi/KKNxOTJES1U/hqdefault.jpg',
        ];
        $video_obj = $this->video->thumbnails($thumbnail);
        $this->assertEquals($video_obj, $expected);
    }

    public function tearDown()
    {
        parent::tearDown();
        M::close();
    }
}
