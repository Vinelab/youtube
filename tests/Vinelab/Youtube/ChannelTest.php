<?php namespace Vinelab\Youtube\Tests;

use Mockery as M;
use Vinelab\Youtube\Channel;
use Vinelab\Youtube\VideoCollection;

class ChannelTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->channel = new Channel();
    }

    public function test_making_channel_using_fill_method()
    {
        $channel_info = M::mock(new \stdClass());
        $channel_info->kind = 'youtube#channelListResponse';
        $channel_info->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass());
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel_info->pageInfo = $pageInfo;
        //items
        $items = [];
        $items[0] = M::mock(new \stdClass());
        $items[0]->kind = 'youtube#channel';
        $items[0]->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/53nIEDRhzjlzEN0ZZf1TQ5Oyr_0"';
        $items[0]->id = 'UCBsKiXTgZrg0tqz4yz_R5Tw';

        $items[0]->snippet = M::mock(new \stdClass());
        $items[0]->snippet->title = 'adib hanna';
        $items[0]->snippet->description = '';
        $items[0]->snippet->publishedAt = '2009-12-27T22:46:00.000Z';

        $thumbnails = M::mock(new \stdClass());

        $default = M::mock(new \stdClass());
        $default->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/default.jpg';
        $medium = M::mock(new \stdClass());
        $medium->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/mqdefault.jpg';
        $high = M::mock(new \stdClass());
        $high->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/hqdefault.jpg';

        $thumbnails->default = $default;
        $thumbnails->medium = $medium;
        $thumbnails->high = $high;
        $items[0]->snippet->thumbnails = $thumbnails;

        $contentDetails = M::mock(new \stdClass());

        $relatedPlaylists = M::mock(new \stdClass());
        $relatedPlaylists->likes = 'LLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->favorites = 'FLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->uploads = 'UUBsKiXTgZrg0tqz4yz_R5Tw';

        $contentDetails->relatedPlaylists = $relatedPlaylists;
        $contentDetails->googlePlusUserId = '113619238331121062947';

        $items[0]->contentDetails = $contentDetails;

        $channel_info->items = $items;

        $videos = M::mock(new VideoCollection());

        $channel_obj = $this->channel->make($channel_info, $videos);
        $this->assertInstanceOf('Vinelab\Youtube\Channel', $channel_obj);
    }

    public function test_set_data()
    {
        $kind = 'test';
        $etag = 'test';
        $sync_enabled = 'test';
        $id = 'test';
        $synced_at = 'test';
        $title = 'test';
        $description = 'test';
        $published_at = 'test';
        $default_thumb = 'test';
        $medium_thumb = 'test';
        $high_thumb = 'test';
        $playlist_likes = 'test';
        $playlist_uploads = 'test';
        $google_plus_user_id = 'test';
        $videos = M::mock(new VideoCollection());

        $channel_obj = $this->channel->setData($kind,
                            $etag,
                            $sync_enabled,
                            $id,
                            $synced_at,
                            $title,
                            $description,
                            $published_at,
                            $default_thumb,
                            $medium_thumb,
                            $high_thumb,
                            $playlist_likes,
                            $playlist_uploads,
                            $google_plus_user_id,
                            $videos);

        $this->assertEquals($channel_obj, null);
    }

    public function test_url()
    {
        $channel_info = M::mock(new \stdClass());
        $channel_info->kind = 'youtube#channelListResponse';
        $channel_info->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass());
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel_info->pageInfo = $pageInfo;
        //items
        $items = [];
        $items[0] = M::mock(new \stdClass());
        $items[0]->kind = 'youtube#channel';
        $items[0]->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/53nIEDRhzjlzEN0ZZf1TQ5Oyr_0"';
        $items[0]->id = 'UCBsKiXTgZrg0tqz4yz_R5Tw';

        $items[0]->snippet = M::mock(new \stdClass());
        $items[0]->snippet->title = 'adib hanna';
        $items[0]->snippet->description = '';
        $items[0]->snippet->publishedAt = '2009-12-27T22:46:00.000Z';

        $thumbnails = M::mock(new \stdClass());

        $default = M::mock(new \stdClass());
        $default->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/default.jpg';
        $medium = M::mock(new \stdClass());
        $medium->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/mqdefault.jpg';
        $high = M::mock(new \stdClass());
        $high->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/hqdefault.jpg';

        $thumbnails->default = $default;
        $thumbnails->medium = $medium;
        $thumbnails->high = $high;
        $items[0]->snippet->thumbnails = $thumbnails;

        $contentDetails = M::mock(new \stdClass());

        $relatedPlaylists = M::mock(new \stdClass());
        $relatedPlaylists->likes = 'LLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->favorites = 'FLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->uploads = 'UUBsKiXTgZrg0tqz4yz_R5Tw';

        $contentDetails->relatedPlaylists = $relatedPlaylists;
        $contentDetails->googlePlusUserId = '113619238331121062947';

        $items[0]->contentDetails = $contentDetails;

        $channel_info->items = $items;

        $videos = M::mock(new VideoCollection());

        $channel_obj = $this->channel->make($channel_info, $videos);

        $url = $channel_obj->url();

        $expected = 'https://www.youtube.com/channel/UCBsKiXTgZrg0tqz4yz_R5Tw';
        $this->assertEquals($url, $expected);
    }

    public function test_returning_the_thumbnails()
    {
        $thumbnails = M::mock(new \stdClass());

        $default = M::mock(new \stdClass());
        $default->url = 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s88-c-k-no/photo.jpg';

        $medium = M::mock(new \stdClass());
        $medium->url = 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s240-c-k-no/photo.jpg';

        $high = M::mock(new \stdClass());
        $high->url = 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s240-c-k-no/photo.jpg';

        $thumbnails->default = $default;
        $thumbnails->medium = $medium;
        $thumbnails->high = $high;

        $expected = [
          'default' => 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s88-c-k-no/photo.jpg',
          'medium' => 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s240-c-k-no/photo.jpg',
          'high' => 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s240-c-k-no/photo.jpg',
        ];
        $thumbnails_obj = $this->channel->thumbnails($thumbnails);

        $this->assertEquals($thumbnails_obj, $expected);
    }

    public function test_returning_content_details()
    {
        //this is not working.
        // $relatedPlaylists = M::mock(new \stdClass);

        // @todo this is working. check how to allow for mocking in this case.
        $relatedPlaylists = new \stdClass();
        $relatedPlaylists->likes        = 'LLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->favorites    = 'FLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->uploads      = 'UUBsKiXTgZrg0tqz4yz_R5Tw';

        $content_details = [
            'relatedPlaylists'  =>  $relatedPlaylists,
            'googlePlusUserId'  =>  '113619238331121062947',
        ];

        $expected = [
            'relatedPlaylists' => [
                'likes'     =>  'LLBsKiXTgZrg0tqz4yz_R5Tw',
                'favorites' =>  'FLBsKiXTgZrg0tqz4yz_R5Tw',
                'uploads'   =>  'UUBsKiXTgZrg0tqz4yz_R5Tw',
            ],
            'googlePlusUserId' => '113619238331121062947',
        ];

        $result = $this->channel->contentDetails($content_details);

        $this->assertEquals($result, $expected);
    }

    public function test_setting_the_videos()
    {
        $video_collection = M::mock(new VideoCollection());
        $result = $this->channel->setVideos($video_collection);
        $this->assertNull($result);
    }

    public function test_magic_method_get()
    {
        $channel_info = M::mock(new \stdClass());
        $channel_info->kind = 'youtube#channelListResponse';
        $channel_info->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass());
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel_info->pageInfo = $pageInfo;
        //items
        $items = [];
        $items[0] = M::mock(new \stdClass());
        $items[0]->kind = 'youtube#channel';
        $items[0]->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/53nIEDRhzjlzEN0ZZf1TQ5Oyr_0"';
        $items[0]->id = 'UCBsKiXTgZrg0tqz4yz_R5Tw';

        $items[0]->snippet = M::mock(new \stdClass());
        $items[0]->snippet->title = 'adib hanna';
        $items[0]->snippet->description = '';
        $items[0]->snippet->publishedAt = '2009-12-27T22:46:00.000Z';

        $thumbnails = M::mock(new \stdClass());

        $default = M::mock(new \stdClass());
        $default->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/default.jpg';
        $medium = M::mock(new \stdClass());
        $medium->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/mqdefault.jpg';
        $high = M::mock(new \stdClass());
        $high->url = 'https://i1.ytimg.com/vi/KKNxOTJES1U/hqdefault.jpg';

        $thumbnails->default = $default;
        $thumbnails->medium = $medium;
        $thumbnails->high = $high;
        $items[0]->snippet->thumbnails = $thumbnails;

        $contentDetails = M::mock(new \stdClass());

        $relatedPlaylists = M::mock(new \stdClass());
        $relatedPlaylists->likes = 'LLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->favorites = 'FLBsKiXTgZrg0tqz4yz_R5Tw';
        $relatedPlaylists->uploads = 'UUBsKiXTgZrg0tqz4yz_R5Tw';

        $contentDetails->relatedPlaylists = $relatedPlaylists;
        $contentDetails->googlePlusUserId = '113619238331121062947';

        $items[0]->contentDetails = $contentDetails;

        $channel_info->items = $items;

        $videos = M::mock(new VideoCollection());

        $channel_obj = $this->channel->make($channel_info, $videos);

        $expected = 'UCBsKiXTgZrg0tqz4yz_R5Tw';
        $result = $channel_obj->id;
        $this->assertEquals($result, $expected);
    }

    public function tearDown()
    {
        parent::tearDown();
        M::close();
    }
}
