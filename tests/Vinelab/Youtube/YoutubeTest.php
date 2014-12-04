<?php namespace Vinelab\Youtube\Tests;

use Mockery as M;
use Vinelab\Youtube\Youtube;

class YoutubeTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        
        $this->manager = M::mock('Vinelab\Youtube\Contracts\ManagerInterface');
        $this->youtube = new Youtube($this->manager);
    }

    public function test_fetching_video()
    {
        $expected = M::mock('Vinelab\Youtube\Video');
        $url = 'https://www.youtube.com/watch?v=KKNxOTJES1U';
        $this->manager->shouldReceive('video')
                    ->with($url)
                    ->andReturn($expected);

        $video = $this->youtube->video($url);
        
        $this->assertEquals($expected, $video);
    }

    public function test_fetching_channel()
    {
        $expected = M::mock('Vinelab\Youtube\Channel');
        $url = 'https://www.youtube.com/channel/UCBsKiXTgZrg0tqz4yz_R5Tw';
        $this->manager->shouldReceive('videosForChannel')
                        ->with($url, '')
                        ->andReturn($expected);

        $channel = $this->youtube->channel($url, null);

        $this->assertEquals($expected, $channel);
    }

    public function test_fetching_channel_with_synced_at()
    {
        $expected = M::mock('Vinelab\Youtube\Channel');
        $url = 'https://www.youtube.com/channel/UCBsKiXTgZrg0tqz4yz_R5Tw';
        $this->manager->shouldReceive('videosForChannel')
                        ->with($url, '2014-05-08T00:00:00Z')
                        ->andReturn($expected);

        $channel = $this->youtube->channel($url, '2014-05-08T00:00:00Z');

        $this->assertEquals($expected, $channel);
    }

//    public function test_syncing_a_video()
//    {
//        $video_param = M::mock('Vinelab\Youtube\Video');
//        $expected = M::mock('Vinelab\Youtube\Video');
//        $url = 'https://www.youtube.com/watch?v=KKNxOTJES1U';
//        $this->manager->shouldReceive('sync')
//                        ->with($video_param)
//                        ->andReturn($expected);
//
//        $video = $this->manager->sync($video_param);
//
//        $this->assertEquals($expected, $video);
//    }

    public function tearDown()
    {
        parent::tearDown();
        M::close();
    }
}
