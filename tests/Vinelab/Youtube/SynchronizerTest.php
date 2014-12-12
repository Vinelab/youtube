<?php namespace Vinelab\Youtube\Tests;

use Illuminate\Support\Collection;
use StdClass;
use Mockery as M;
use Vinelab\Youtube\Video;
use Vinelab\Youtube\Channel;
use Vinelab\Youtube\Synchronizer;
use Vinelab\Youtube\VideoCollection;
use Vinelab\Youtube\Contracts\VideoInterface;
use Vinelab\Youtube\Contracts\ChannelInterface;
use Vinelab\Youtube\Contracts\VideoManagerInterface;
use Vinelab\Youtube\Contracts\SynchronizerInterface;
use Vinelab\Youtube\Contracts\YoutubeParserInterface;
use Vinelab\Youtube\Exceptions\IncompatibleParametersException;

class SynchronizerTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->video = new Video();
        $this->channel = new Channel();
        // $this->video =1 M::mock('Vinelab\Youtube\Contracts\VideoInterface');
        $this->mchannel = M::mock('Vinelab\Youtube\Contracts\ChannelInterface');
        $this->mapi = M::mock('Vinelab\Youtube\Api');
        $this->synchronizer = new Synchronizer($this->mapi, $this->mchannel);
    }

    public function test_sync_with_non_changed_video()
    {
        $m_video = M::mock('Vinelab\Youtube\YoutubeVideoInterface');
        $m_video->sync_enabled = true;
        $m_video->etag = 'etag_1';

        $m_video->shouldReceive('getYoutubeInfo')->once()->andReturn(['youtube_id' => '9bZkp7q19f0']);

        $response = new \stdClass;
        $response->etag = 'etag_1';

        $this->mapi->shouldReceive('video')->once()->andReturn($response);

        $this->assertInstanceOf('Vinelab\Youtube\ResourceInterface', $m_video);
        $this->assertInstanceOf('Vinelab\Youtube\YoutubeVideoInterface', $m_video);

        $result = $this->synchronizer->sync($m_video);

        $this->assertEmpty($result);
    }

    public function test_sync_with_changed_video()
    {
        $m_video = M::mock('Vinelab\Youtube\YoutubeVideoInterface');
        $m_video->sync_enabled = true;
        $m_video->etag = 'etag_1';

        $m_video->shouldReceive('getYoutubeInfo')->once()->andReturn(['youtube_id' => '9bZkp7q19f0']);

        $response = new \stdClass;
        $response->etag = 'etag_2';

        $this->mapi->shouldReceive('video')->once()->andReturn($response);

        $this->assertInstanceOf('Vinelab\Youtube\ResourceInterface', $m_video);
        $this->assertInstanceOf('Vinelab\Youtube\YoutubeVideoInterface', $m_video);

        $result = $this->synchronizer->sync($m_video);

        $this->assertNotEmpty($result);
        $this->assertEquals($response, $result);
    }


    /**
     * this function is used by the tests of the sync function
     *
     * @param null $request_video_1_id
     * @param null $request_video_1_etag
     * @param bool $request_video_1_sync
     * @param null $request_video_2_id
     * @param null $request_video_2_etag
     * @param bool $request_video_2_sync
     * @param null $response_video_1_id
     * @param null $response_video_1_etag
     * @param null $response_video_2_id
     * @param null $response_video_2_etag
     *
     * @return mixed
     */
    private function sync_test_biulder(
        $request_video_1_id = null,
        $request_video_1_etag = null,
        $request_video_1_sync = true,
        $request_video_2_id = null,
        $request_video_2_etag = null,
        $request_video_2_sync = true,
        $response_video_1_id = null,
        $response_video_1_etag = null,
        $response_video_2_id = null,
        $response_video_2_etag = null
    ) {

        // + Response creation (this is the response that should be sent from the youtube api)

        // 1. initializing video to be added to channel response
        $thumbnail_data = new StdClass();
        $thumbnail_data->url = 'https://i.ytimg.com/vi/zzz/default.jpg';

        $thumbnail = new StdClass();
        $thumbnail->default = $thumbnail_data;
        $thumbnail->medium = $thumbnail_data;
        $thumbnail->high = $thumbnail_data;

        $snippet = new StdClass();
        $snippet->publishedAt = '2014-04-16T16:41:42.000Z';
        $snippet->channelId = 'channel-id-WHATEVER';
        $snippet->title = 'youtube video title WHATEVER';
        $snippet->description = 'youtube video description WHATEVER';
        $snippet->thumbnails = $thumbnail;

        $video_id_1 = new \stdClass;
        $video_id_1->kind = 'youtube#video';
        $video_id_1->videoId = $response_video_1_id;

        $video_id_2 = new \stdClass;
        $video_id_2->kind = 'youtube#video';
        $video_id_2->videoId = $response_video_2_id;

        $response_video_1_data = new StdClass();
        $response_video_1_data->kind = 'youtube#searchResult';
        $response_video_1_data->etag = $response_video_1_etag;
        $response_video_1_data->id = $video_id_1;
        $response_video_1_data->snippet = $snippet;
        $response_video_1_data->channelTitle = 'youtube channel title 1';
        $response_video_1_data->liveBroadcastContent = 'none';

        $response_video_2_data = new StdClass();
        $response_video_2_data->kind = 'youtube#searchResult';
        $response_video_2_data->etag = $response_video_2_etag;
        $response_video_2_data->id = $video_id_2;
        $response_video_2_data->snippet = $snippet;
        $response_video_2_data->channelTitle = 'youtube channel title 2';
        $response_video_2_data->liveBroadcastContent = 'none';

        // initializing the video maker, that will convert the above data into video object
        $video_maker = new Video();
        $response_video_1 = $video_maker->make($response_video_1_data);
        if($response_video_2_id)
            $response_video_2 = $video_maker->make($response_video_2_data);

        // 2. initializing channel response itself
        $pageInfo = new StdClass();
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 1;

        $related_playlists = new StdClass();
        $related_playlists->uploads = 'UUpOLwC-MKK9STITTUAYlmVQ';

        $content_details = new StdClass();
        $content_details->relatedPlaylists = $related_playlists;
        $content_details->googlePlusUserId = '123';

        $item = new StdClass();
        $item->kind = 'youtube#channel';
        $item->etag = 'local-channel-etag';
        $item->id = 'youtube-channel-id-1';
        $item->snippet = $snippet;
        $item->contentDetails = $content_details;

        $response_channel_data = new StdClass();
        $response_channel_data->kind = 'youtube#channelListResponse';
        $response_channel_data->etag = 'local-channel-etag';
        $response_channel_data->pageInfo = $pageInfo;
        $response_channel_data->items = [$item];

        // initializing the channel maker, that will convert the above data into channel object
        if(! $response_video_2_id)
            $videos_collection = new VideoCollection([$response_video_1]);
        else
            $videos_collection = new VideoCollection([$response_video_1, $response_video_2]);

        $channel_maker = new Channel();
        $youtube_api_response = $channel_maker->make($response_channel_data, $videos_collection);

        // mocking the youtube call to return the pre made response
        $this->mapi->shouldReceive('channel')->andReturn($youtube_api_response);




        // + Request creation (now preparing the request object [a channel object from our database])

        // creating videos
        $request_video_1 = M::mock('Vinelab\Youtube\YoutubeVideoInterface');
        $request_video_1->sync_enabled = $request_video_1_sync;
        $request_video_1->kind = 'youtube#video';
        $request_video_1->id = $request_video_1_id;
        $request_video_1->etag = $request_video_1_etag;
        $request_video_1->shouldReceive('getYoutubeInfo')->andReturn(['youtube_id' => $request_video_1_id]);

        $request_video_2 = M::mock('Vinelab\Youtube\YoutubeVideoInterface');
        $request_video_2->sync_enabled = $request_video_2_sync;
        $request_video_2->kind = 'youtube#video';
        $request_video_2->id = $request_video_2_id;
        $request_video_2->etag = $request_video_2_etag;
        $request_video_2->shouldReceive('getYoutubeInfo')->andReturn(['youtube_id' => $request_video_2_id]);

        // creating chanel of the above videos
        if(! $request_video_2_id)
            $collection_1 = Collection::make([$request_video_1]);
        else
            $collection_1 = Collection::make([$request_video_1, $request_video_2]);

        $channel_id = 'local-channel-id-1';
        $request_channel = M::mock('Vinelab\Youtube\YoutubeChannelInterface');
        $request_channel->sync_enabled = true;
        $request_channel->etag = 'local-channel-etag-1';
        $request_channel->synced_at = '2009-12-27T22:46:00.000Z';
        $request_channel->videos = $collection_1;
        $request_channel->shouldReceive('getYoutubeInfo')->andReturn(['youtube_id' => $channel_id]);

        // syncing our local channel with youtube
        $result = $this->synchronizer->sync($request_channel);

        return $result;
    }



    public function test_sync_channel_with_non_changed_video()
    {
        // local videos
        $request_video_1_id = '111';
        $request_video_1_etag = 'eee';
        $request_video_1_sync = true;
        $request_video_2_id = null;
        $request_video_2_etag = null;
        $request_video_2_sync = true;
        // youtube videos
        $response_video_1_id = '111';
        $response_video_1_etag = 'eee';
        $response_video_2_id = null;
        $response_video_2_etag = null;


        $result = $this->sync_test_biulder(
            $request_video_1_id,
            $request_video_1_etag,
            $request_video_1_sync,
            $request_video_2_id,
            $request_video_2_etag,
            $request_video_2_sync,
            $response_video_1_id,
            $response_video_1_etag,
            $response_video_2_id,
            $response_video_2_etag
        );

        // asserting I got the old video from local
        $this->assertEquals($result[0]->id, $request_video_1_id);
        $this->assertEquals($result[0]->etag, $request_video_1_etag);


    }


    public function test_sync_channel_with_new_video_on_youtube()
    {
        // local videos
        $request_video_1_id = '111';
        $request_video_1_etag = 'eee';
        $request_video_1_sync = true;
        $request_video_2_id = null;
        $request_video_2_etag = null;
        $request_video_2_sync = true;
        // youtube videos
        $response_video_1_id = '111';
        $response_video_1_etag = 'eee';
        $response_video_2_id = '222';
        $response_video_2_etag = 'ttt';


        $result = $this->sync_test_biulder(
            $request_video_1_id,
            $request_video_1_etag,
            $request_video_1_sync,
            $request_video_2_id,
            $request_video_2_etag,
            $request_video_2_sync,
            $response_video_1_id,
            $response_video_1_etag,
            $response_video_2_id,
            $response_video_2_etag
        );

        // asserting I got the old video from local
        $this->assertEquals($result[0]->id, $request_video_1_id);
        $this->assertEquals($result[0]->etag, $request_video_1_etag);

        // assert I got the new video form youtube
        $this->assertEquals($result[1]->id, $response_video_2_id);
        $this->assertEquals($result[1]->etag, $response_video_2_etag);

    }





    public function test_sync_channel_with_updated_video_and_sync_enabled()
    {
        // local videos
        $request_video_1_id = '111';
        $request_video_1_etag = 'ddd';
        $request_video_1_sync = true;

        $request_video_2_id = null;
        $request_video_2_etag = null;
        $request_video_2_sync = true;

        // youtube videos
        $response_video_1_id = '111';
        $response_video_1_etag = 'eee';

        $response_video_2_id = null;
        $response_video_2_etag = null;


        $result = $this->sync_test_biulder(
            $request_video_1_id,
            $request_video_1_etag,
            $request_video_1_sync,
            $request_video_2_id,
            $request_video_2_etag,
            $request_video_2_sync,
            $response_video_1_id,
            $response_video_1_etag,
            $response_video_2_id,
            $response_video_2_etag
        );

        // asserting I got the updated video from youtube
        $this->assertEquals($result[0]->id, $response_video_1_id);
        $this->assertEquals($result[0]->etag, $response_video_1_etag);
    }




    public function test_sync_channel_with_updated_video_and_sync_disabled()
    {
        // local videos
        $request_video_1_id = '111';
        $request_video_1_etag = 'ddd';
        $request_video_1_sync = false;

        $request_video_2_id = null;
        $request_video_2_etag = null;
        $request_video_2_sync = true;

        // youtube videos
        $response_video_1_id = '111';
        $response_video_1_etag = 'eee';

        $response_video_2_id = null;
        $response_video_2_etag = null;


        $result = $this->sync_test_biulder(
            $request_video_1_id,
            $request_video_1_etag,
            $request_video_1_sync,
            $request_video_2_id,
            $request_video_2_etag,
            $request_video_2_sync,
            $response_video_1_id,
            $response_video_1_etag,
            $response_video_2_id,
            $response_video_2_etag
        );

        // asserting I got the updated video from youtube
        $this->assertEquals($result[0]->id, $request_video_1_id);
        $this->assertEquals($result[0]->etag, $request_video_1_etag);
    }





    public function test_video_diff_with_two_different_etags()
    {
        $resource = M::mock(new \stdClass);
        $resource->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_1';

        $resource->id = $id;
        $resource->etag = 'etag_1';
        $resource->sync_enabled = true;
        $resource->synced_at    = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass;
        $snippet->thumbnails = new \stdClass;

        $resource->snippet = $snippet;
        $resource->thumbnails = [
            'default'   =>  [
                'url'   =>  'example1'
            ],
            'medium'   =>  [
                'url'   =>  'example1'
            ],
            'high'   =>  [
                'url'   =>  'example1'
            ]
        ];

        $resource = $this->video->make($resource);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $resource);

        $response = M::mock(new \stdClass);
        $response->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_2';

        $response->id = $id;
        $response->etag = 'etag_2';
        $response->sync_enabled = true;
        $response->synced_at    = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass;
        $snippet->thumbnails = new \stdClass;

        $response->snippet = $snippet;
        $response->thumbnails = [
            'default'   =>  [
                'url'   =>  'example1'
            ],
            'medium'   =>  [
                'url'   =>  'example1'
            ],
            'high'   =>  [
                'url'   =>  'example1'
            ]
        ];

        $response = $this->video->make($response);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $response);

        $result = $this->callProtectedMethod($this->synchronizer, 'videoDiff', $resource, $response);
        $this->assertTrue($result);
    }

    public function test_video_diff_with_two_similar_etags()
    {
        $resource = M::mock(new \stdClass);
        $resource->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_1';

        $resource->id = $id;
        $resource->etag = 'etag';
        $resource->sync_enabled = true;
        $resource->synced_at    = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass;
        $snippet->thumbnails = new \stdClass;

        $resource->snippet = $snippet;
        $resource->thumbnails = [
            'default'   =>  [
                'url'   =>  'example1'
            ],
            'medium'   =>  [
                'url'   =>  'example1'
            ],
            'high'   =>  [
                'url'   =>  'example1'
            ]
        ];

        $resource = $this->video->make($resource);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $resource);

        $response = M::mock(new \stdClass);
        $response->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_2';

        $response->id = $id;
        $response->etag = 'etag';
        $response->sync_enabled = true;
        $response->synced_at    = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass;
        $snippet->thumbnails = new \stdClass;

        $response->snippet = $snippet;
        $response->thumbnails = [
            'default'   =>  [
                'url'   =>  'example1'
            ],
            'medium'   =>  [
                'url'   =>  'example1'
            ],
            'high'   =>  [
                'url'   =>  'example1'
            ]
        ];

        $response = $this->video->make($response);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $response);

        $result = $this->callProtectedMethod($this->synchronizer, 'videoDiff', $resource, $response);

        $this->assertFalse($result);
    }

    public function test_setting_channel_data()
    {
        $channel_info = M::mock(new \stdClass);
        $channel_info->kind = 'youtube#channelListResponse';
        $channel_info->etag = '"asdsUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass);
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel_info->pageInfo = $pageInfo;
        //items
        $item = [];
        $items[0] = M::mock(new \stdClass);
        $items[0]->kind = 'youtube#channel';
        $items[0]->etag = '"asdsUfYBkeQ2ncpP9OLHKB0fDw4/53nIEDRhzjlzEN0ZZf1TQ5Oyr_0"';
        $items[0]->id = 'UCBsKiXTgZrg0tqz4yz_R5Tw';

        $items[0]->snippet = M::mock(new \stdClass);
        $items[0]->snippet->title = 'adib hanna';
        $items[0]->snippet->description = '';
        $items[0]->snippet->publishedAt = '2009-12-27T22:46:00.000Z';

        $thumbnails = M::mock(new \stdClass);

        $default = M::mock(new \stdClass);
        $default->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/default.jpg';
        $medium = M::mock(new \stdClass);
        $medium->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/mqdefault.jpg';
        $high = M::mock(new \stdClass);
        $high->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/hqdefault.jpg';

        $thumbnails->default = $default;
        $thumbnails->medium = $medium;
        $thumbnails->high = $high;
        $items[0]->snippet->thumbnails = $thumbnails;


        $contentDetails = M::mock(new \stdClass);

        $relatedPlaylists = M::mock(new \stdClass);
        $relatedPlaylists->likes = 'LLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->favorites = 'FLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->uploads = 'UUBsKiXTgZrg0tqz4yz_R5Tw';

        $contentDetails->relatedPlaylists = $relatedPlaylists;
        $contentDetails->googlePlusUserId = '113619238331121062947';

        $items[0]->contentDetails = $contentDetails;

        $channel_info->items = $items;

        $videos = M::mock(new VideoCollection);

        $response = $this->channel->make($channel_info, $videos);

        $this->assertInstanceOf('Vinelab\Youtube\Channel', $response);

        $result = $this->callProtectedMethod($this->synchronizer, 'setChannelData', $response);

        $this->assertNull($result);
    }

    //this is used whenever we have a protected method
    public function callProtectedMethod($object, $methodName)
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        $params = array_slice(func_get_args(), 2); //get all the parameters after $methodName
        return $reflectionMethod->invokeArgs($object, $params);
    }

    public function tearDown()
    {
        parent::tearDown();
        M::close();
    }
}
