<?php namespace Vinelab\Youtube\Tests;

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
        // $this->video = M::mock('Vinelab\Youtube\Contracts\VideoInterface');
        $this->mchannel = M::mock('Vinelab\Youtube\Contracts\ChannelInterface');
        $this->synchronizer = new Synchronizer($this->mchannel);
    }

    public function test_sync_method_with_two_videos()
    {
        $response = M::mock(new \stdClass);
        $response->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_1';

        $response->id = $id;
        $response->etag = 'etag_1';
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

        $video_obj_1 = $this->video->make($response);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $video_obj_1);

        $resource = M::mock(new \stdClass);
        $resource->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_2';

        $resource->id = $id;
        $resource->etag = 'etag_2';
        $resource->sync_enabled = true;
        $resource->synced_at    = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass;
        $snippet->thumbnails = new \stdClass;

        $resource->snippet = $snippet;
        $resource->thumbnails = [
                        'default'   =>  [
                            'url'   =>  'example2'
                        ],
                        'medium'   =>  [
                            'url'   =>  'example2'
                        ],
                        'high'   =>  [
                            'url'   =>  'example2'
                        ]
                    ];

        $video_obj_2 = $this->video->make($resource);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $video_obj_2);

        $result = $this->synchronizer->sync($video_obj_2, $video_obj_1);

        $this->assertInstanceOf('Vinelab\Youtube\Video',$result);
    }

    public function test_sync_method_with_two_channels()
    {
        $channel_info = M::mock(new \stdClass);
        $channel_info->kind = 'youtube#channelListResponse';
        $channel_info->etag = '"abcsUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass);
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel_info->pageInfo = $pageInfo;
        //items
        $items = [];
        $items[0] = M::mock(new \stdClass);
        $items[0]->kind = 'youtube#channel';
        $items[0]->etag = '"abcsUfYBkeQ2ncpP9OLHKB0fDw4/53nIEDRhzjlzEN0ZZf1TQ5Oyr_0"';
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

        $resource = $this->channel->make($channel_info, $videos); 

        $this->assertInstanceOf('Vinelab\Youtube\Channel', $resource);

        //***
        $channel_info = M::mock(new \stdClass);
        $channel_info->kind = 'youtube#channelListResponse';
        $channel_info->etag = '"asdsUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass);
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel_info->pageInfo = $pageInfo;
        //items
        $items = [];
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

        $result = $this->synchronizer->sync($resource, $response);
        $this->assertInstanceOf('Vinelab\Youtube\Channel',$result);
    }

    /**
     * @expectedException Vinelab\Youtube\Exceptions\IncompatibleParametersException
     */
    public function test_sync_method_with_one_channel_and_one_video()
    {
        $response = M::mock(new \stdClass);
        $response->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_1';

        $response->id = $id;
        $response->etag = 'etag_1';
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

        $video_obj_1 = $this->video->make($response);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $video_obj_1);

        $resource = M::mock(new \stdClass);
        $resource->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_2';

        $resource->id = $id;
        $resource->etag = 'etag_2';
        $resource->sync_enabled = true;
        $resource->synced_at    = date("Y-m-d\TH:i:sP");

        $snippet = new \stdClass;
        $snippet->thumbnails = new \stdClass;

        $resource->snippet = $snippet;
        $resource->thumbnails = [
                        'default'   =>  [
                            'url'   =>  'example2'
                        ],
                        'medium'   =>  [
                            'url'   =>  'example2'
                        ],
                        'high'   =>  [
                            'url'   =>  'example2'
                        ]
                    ];

        $resource = $this->video->make($resource);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $resource);

        $channel_info = M::mock(new \stdClass);
        $channel_info->kind = 'youtube#channelListResponse';
        $channel_info->etag = '"asdsUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass);
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel_info->pageInfo = $pageInfo;
        //items
        $items = [];
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

        $result = $this->synchronizer->sync($resource, $response);
    }

    /**
     * @expectedException Vinelab\Youtube\Exceptions\IncompatibleParametersException
     */
    public function test_sync_method_with_one_null_channel_and_one_video()
    {
        $response = M::mock(new \stdClass);
        $response->kind = "youtube#video";
        $id = new \stdClass;
        $id->kind = 'youtube#video';
        $id->videoId = 'video_id_1';

        $response->id = $id;
        $response->etag = 'etag_1';
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

        $video_obj_1 = $this->video->make($response);
        $this->assertInstanceOf('Vinelab\Youtube\Video', $video_obj_1);

        $resource = null;

        $result = $this->synchronizer->sync($resource, $response);
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
        $items = [];
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

    public function test_sync_videos()
    {
        
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