<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\Contracts\PlaylistInterface;

class Playlist implements PlaylistInterface, ResourceInterface {

    /**
     * The Playlist Data
     * @var array
     */
    protected $data = [];

    /**
     * Istantiate an instance of this class.
     * @param  stdClass                        $playlist_info
     * @param  Vinelab\Youtube\VideoCollection $videos
     * @return Vinelab\Youtube\Playlist
     */
    public function make($playlist_info, VideoCollection $videos)
    {
        $playlist = new static();

        $playlist->fill((array) $playlist_info, $videos);

        return $playlist;
    }

    /**
     * fill the Playlist object with its data
     * @param array                           $playlist_info
     * @param Vinelab\Youtube\VideoCollection $videos
     */
    protected function fill(array $playlist_info, VideoCollection $videos)
    {
        $items = (array) $playlist_info['items'][0];
        $kind             =   $items['kind'];
        $etag             =   $items['etag'];
        $sync_enabled     =   true;
        $id               =   $items['id'];
        $synced_at        =   date("Y-m-d\TH:i:sP");

        $snippet        =   (array) $items['snippet'];
        $title          =   $snippet['title'];
        $description    =   $snippet['description'];
        $published_at   =   $snippet['publishedAt'];

        $thumbnails         =   $this->thumbnails($items['snippet']->thumbnails);
        $default_thumb      =   $thumbnails['default'];
        $medium_thumb       =   $thumbnails['medium'];
        $high_thumb         =   $thumbnails['high'];

        unset($snippet['thumbnails']);

        // similar to $this->data = compact('kind'...)
        $this->setData(
                    $kind,
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
                    $videos
                );
    }

    /**
     * setData the data
     * @param  string  $kind
     * @param  stringt $etag
     * @param  boolean $sync_enabled
     * @param  string  $id
     * @param  string  $synced_at
     * @param  string  $title
     * @param  string  $description
     * @param  string  $published_at
     * @param  string  $default_thumb
     * @param  string  $medium_thumb
     * @param  string  $high_thumb
     * @param  string  $playlist_likes
     * @param  string  $playlist_uploads
     * @param  string  $google_plus_user_id
     * @param  string  $videos
     * @return array
     */
    public function setData(
        $kind,
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
        $videos
    ) {
        $this->data = compact('kind',
                      'etag',
                      'sync_enabled',
                      'id',
                      'synced_at',
                      'title',
                      'description',
                      'published_at',
                      'default_thumb',
                      'medium_thumb',
                      'high_thumb',
                      'videos');
    }

    /**
     * the playlist url
     * @return string
     */
    public function url()
    {
        return 'https://www.youtube.com/playlist/'.$this->id;
    }

    /**
     * return an associative array of the thumbnails
     * @param  stdClass $thumbnails
     * @return array
     */
    public function thumbnails($thumbnails)
    {
        $thumbs = [];
        foreach ($thumbnails as $key => $thumbnail) {
            $thumbs[$key] = $thumbnail->url;
        }

        return $thumbs;
    }

    /**
     * set the video
     * @param VideoCollection $video_collection
     */
    public function setVideos($video_collection)
    {
        $this->data['videos'] = $video_collection;
    }

    /**
     * magic method used to access the protected
     * Playlist data.
     * @param  string $name
     * @return string
     */
    public function __get($name)
    {
        return (isset($this->data[$name])) ? $this->data[$name] : null;
    }

    /**
     * Return the Playlist data
     * @return array
     */
    public function getYoutubeInfo()
    {
        return $this->data;
    }

    /**
     * return the playlist id
     * @return integer
     */
    public function id()
    {
        return $this->data['id'];
    }

    /**
     * @return mixed
     */
    public function youtubeSyncedAt()
    {
        return $this->data['synced_at'];
    }
}
