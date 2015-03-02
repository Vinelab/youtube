<?php namespace Vinelab\Youtube\Tests;

use Mockery as M;
use Vinelab\Youtube\Video;
use Vinelab\Youtube\Parser;
use Vinelab\Youtube\Contracts\VideoInterface;
use Vinelab\Youtube\Contracts\ChannelInterface;
use Vinelab\Youtube\tests\TestCase;

class ParserTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->video = new Video();
        // $this->video = M::mock('Vinelab\Youtube\Contracts\VideoInterface');
        $this->mvideo = M::mock('Vinelab\Youtube\Contracts\VideoInterface');
        $this->mchannel = M::mock('Vinelab\Youtube\Contracts\ChannelInterface');
        $this->mplaylist = M::mock('Vinelab\Youtube\Contracts\PlaylistInterface');
        $this->parser = new Parser($this->mvideo, $this->mchannel, $this->mplaylist);
    }

    //this is giving me an error that i'm unable to solve,
    //BadMethodCallException: Method Mockery_2_Vinelab_Youtube_Video::make() does not exist on this mock object.
    public function test_parse_videos_and_channel()
    {
        $video_pages = [];
        $video_pages[0] = M::mock(new \stdClass());

        $video_pages[0]->kind = 'youtube#searchListResponse';
        $video_pages[0]->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/f1lArhUWCTXVoX0mjsB4XVtW3-M"';

        $pageInfo = M::mock(new \stdClass());
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 20;

        $video_pages[0]->pageInfo = $pageInfo;

        //items
        $items = [];
        $items[0] = M::mock(new \stdClass());
        $items[0]->kind = 'youtube#searchResult';
        $items[0]->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/af1eeVp2yks5Z1PgoBwAtyQK578"';

        $id = M::mock(new \stdClass());
        $id->kind = 'youtube#video';
        $id->videoId = '4l5M0vvOnjc';
        $items[0]->id = $id;

        $items[0]->snippet = M::mock(new \stdClass());
        $items[0]->snippet->publishedAt = '2014-05-01T13:33:39.000Z';
        $items[0]->snippet->channelId = 'UCBsKiXTgZrg0tqz4yz_R5Tw';
        $items[0]->snippet->title = 'Charis - a light to bring change';
        $items[0]->snippet->description = '';

        $thumbnails = M::mock(new \stdClass());

        $default = M::mock(new \stdClass());
        $default->url = 'https://i.ytimg.com/vi/4l5M0vvOnjc/default.jpg';
        $medium = M::mock(new \stdClass());
        $medium->url = 'https://i.ytimg.com/vi/4l5M0vvOnjc/mqdefault.jpg';
        $high = M::mock(new \stdClass());
        $high->url = 'https://i.ytimg.com/vi/4l5M0vvOnjc/hqdefault.jpg';

        $thumbnails->default = $default;
        $thumbnails->medium = $medium;
        $thumbnails->high = $high;
        $items[0]->snippet->thumbnails = $thumbnails;

        $items[0]->snippet->channelTitle = 'Insomnia721';
        $items[0]->snippet->liveBroadcastContent = 'none';
        $video_pages[0]->items = $items;

        //channel
        $channel = M::mock(new \stdClass());
        $channel->kind = 'youtube#channelListResponse';
        $channel->etag = '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/lvtU4VUnpiTDxniIJa_1E7oTlKI"';

        $pageInfo = M::mock(new \stdClass());
        $pageInfo->totalResults = 1;
        $pageInfo->resultsPerPage = 5;

        $channel->pageInfo = $pageInfo;
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

        $channel->items = $items;

        // $result = $this->parser->parse($video_pages, $channel);

        // $this->assertInstanceOf('Vinelab\Youtube\Channel');
    }

    public function tearDown()
    {
        parent::tearDown();
        M::close();
    }
}
