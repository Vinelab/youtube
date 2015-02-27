# Laravel Youtube Library
This library is used for fetching videos and channels from youtube using a URL.

### Installation
1. Include using Composer: `"vinelab/youtube" : "*"`
2. Add the service provider
`'Vinelab\Youtube\YoutubeServiceProvider'`
3. Add the Facade `'Youtube' => 'Vinelab\Youtube\Facades\Youtube'`
4. Publish the config file `php artisan config:publish Vinelab/youtube`
5. Add your key to the config file


### Usage
Use the `Youtube` Facade to access the package's functionalities.


#### `Youtube::video($url)`

> To fetch a video use `Youtube::video($url)`
this will return an object of type `Najem\Videos\Video`.

> You can fetch multiple `videos` at once by passing an `array` of `Url's`, to `Youtube::videos($urls)` or even `Youtube::video($urls)`.

#### Response:

```json
object(Vinelab\Youtube\Video)[180]
  public 'kind' => string 'youtube#video' (length=13)
  public 'id' => string '1j1MBSwg44A' (length=11)
  public 'etag' => string '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/ZsQyzEl5gbrEkz55D3fNHwM1RUM"' (length=57)
  public 'sync_enabled' => boolean true
  public 'synced_at' => string '2014-05-08T15:55:06+00:00' (length=25)
  public 'snippet' => 
    array (size=5)
      'publishedAt' => string '2013-07-02T16:11:38.000Z' (length=24)
      'channelId' => string 'UCUuseKzXVxgBCnSy8KU90jg' (length=24)
      'title' => string 'Pink Floyd - Comfortably Numb (Cover by Hayajan)' (length=48)
      'description' => string 'Hayajan | Ya Bay: Lyrics of Comfortably Numb wr'... (length=675)
      'channelTitle' => string 'Alaa Wardi' (length=10)
  public 'thumbnails' => 
    array (size=5)
      'default' => string 'https://i1.ytimg.com/vi/1j1MBSwg44A/default.jpg' (length=47)
      'medium' => string 'https://i1.ytimg.com/vi/1j1MBSwg44A/mqdefault.jpg' (length=49)
      'high' => string 'https://i1.ytimg.com/vi/1j1MBSwg44A/hqdefault.jpg' (length=49)
      'standard' => string 'https://i1.ytimg.com/vi/1j1MBSwg44A/sddefault.jpg' (length=49)
      'maxres' => string 'https://i1.ytimg.com/vi/1j1MBSwg44A/maxresdefault.jpg' (length=53)
```


#### `Youtube::channel($url)`

> To fetch a channel use `Youtube::channel($url)`
this will return an object of type `Najem\Videos\Channel`.

#### Response:

```json
object(Najem\Videos\Channel)[209]
  protected 'data' =>
    array (size=14)
      'kind' => string 'youtube#channel' (length=15)
      'etag' => string '"ePFRUfYBkeQ2ncpP9OLHKB0fDw4/53nIEDRhzjlzEN0ZZf1TQ5Oyr_0"' (length=57)
      'sync_enabled' => boolean true
      'id' => string 'UCBsKiXTgZrg0tqz4yz_R5Tw' (length=24)
      'synced_at' => string '2014-05-08T15:56:10+00:00' (length=25)
      'title' => string 'adib hanna' (length=10)
      'description' => string '' (length=0)
      'published_at' => string '2009-12-27T22:46:00.000Z' (length=24)
      'default_thumb' => string 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s88-c-k-no/photo.jpg' (length=91)
      'medium_thumb' => string 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s240-c-k-no/photo.jpg' (length=92)
      'high_thumb' => string 'https://yt3.ggpht.com/-xRortO695HE/AAAAAAAAAAI/AAAAAAAAAAA/b2VPZd_ZNsk/s240-c-k-no/photo.jpg' (length=92)
      'playlist_likes' => string 'LLBsKiXTgZrg0tqz4yz_R5Tw' (length=24)
      'playlist_uploads' => string 'UUBsKiXTgZrg0tqz4yz_R5Tw' (length=24)
      'google_plus_user_id' => string '113619238331121062947' (length=21)
      'videos' =>
        object(Najem\Videos\VideoCollection)[177]
          protected 'items' =>
            array (size=2)
      0 =>
        object(Najem\Videos\Video)[207]
          public 'kind' => string 'youtube#video' (length=13)
          public 'id' => string 'cdy2iLDznbI' (length=11)
          public 'etag' => string '"LFawZk2qAkq9bosMnzaQJqPHO_0/lXpe47wULVDBmjpY0A3wMuM2PpQ"' (length=57)
          public 'sync_enabled' => boolean true
          public 'snippet' =>
            array (size=5)
              ...
          public 'thumbnails' =>
            array (size=3)
              ...
      1 =>
        object(Najem\Videos\Video)[208]
          public 'kind' => string 'youtube#video' (length=13)
          public 'id' => string '4l5M0vvOnjc' (length=11)
          public 'etag' => string '"LFawZk2qAkq9bosMnzaQJqPHO_0/af1eeVp2yks5Z1PgoBwAtyQK578"' (length=57)
          public 'sync_enabled' => boolean true
          public 'snippet' =>
            array (size=5)
              ...
          public 'thumbnails' =>
            array (size=3)
              ...
```


#### `Youtube::playlist($url)`

> To fetch a playlist use `Youtube::playlist($url)`
this will return an object of type `Najem\Videos\Playlist`.

#### Response:

```json
object(Vinelab\Youtube\Playlist)[811]
  protected 'data' => 
    array (size=12)
      'kind' => string 'youtube#playlist' (length=16)
      'etag' => string '"9Y5jTkxN1JET3y-M4wKMA5aK7Mk/Ed_sn_8oQKILYPBK-rRE431SEss"' (length=57)
      'sync_enabled' => boolean true
      'id' => string 'PLaMWTg0sIKTdOsNOI9h2AjKHUremHKGNZ' (length=34)
      'synced_at' => string '2015-02-27T14:12:18+00:00' (length=25)
      'title' => string 'Laravel 4.1 Tutorial | مدخل الى برمجة الوب بإستخدام لارافيل' (length=90)
      'description' => string '' (length=0)
      'published_at' => string '2014-06-02T00:47:12.000Z' (length=24)
      'default_thumb' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/default.jpg' (length=46)
      'medium_thumb' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/mqdefault.jpg' (length=48)
      'high_thumb' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/hqdefault.jpg' (length=48)
      'videos' => 
        object(Vinelab\Youtube\VideoCollection)[695]
          protected 'items' => 
            array (size=9)
              0 => 
                object(Vinelab\Youtube\Video)[802]
                  public 'kind' => string 'youtube#playlistItem' (length=20)
                  public 'id' => string 'PLoqlw2-AY6NMK_3wSxsZieCVbVrh7I4ZUMlgYhD46jpk' (length=45)
                  public 'etag' => string '"9Y5jTkxN1JET3y-M4wKMA5aK7Mk/1CE9qGYaTyrvAf_cVZDngkiJNrI"' (length=57)
                  public 'sync_enabled' => boolean false
                  public 'synced_at' => string '2015-02-27T14:12:18+00:00' (length=25)
                  public 'snippet' => 
                    array (size=8)
                      'publishedAt' => string '2014-06-02T00:48:26.000Z' (length=24)
                      'channelId' => string 'UCpOLwC-MKK9STITTUAYlmVQ' (length=24)
                      'title' => string '1 - Laravel 4.1: Introduction | مقدمة الى لارافيل' (length=64)
                      'description' => string '- why Laravel
- what is laravel 4
- walking through the top features
- system and knowlege requirement' (length=102)
                      'channelTitle' => string 'Mahmoud Zalt' (length=12)
                      'playlistId' => string 'PLaMWTg0sIKTdOsNOI9h2AjKHUremHKGNZ' (length=34)
                      'position' => int 0
                      'resourceId' => 
                        object(stdClass)[729]
                          public 'kind' => string 'youtube#video' (length=13)
                          public 'videoId' => string 'FasdiQBlWwM' (length=11)
                  public 'thumbnails' => 
                    array (size=5)
                      'default' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/default.jpg' (length=46)
                      'medium' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/mqdefault.jpg' (length=48)
                      'high' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/hqdefault.jpg' (length=48)
                      'standard' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/sddefault.jpg' (length=48)
                      'maxres' => string 'https://i.ytimg.com/vi/FasdiQBlWwM/maxresdefault.jpg' (length=52)
                  public 'url' => string 'https://www.youtube.com/watch?v=PLoqlw2-AY6NMK_3wSxsZieCVbVrh7I4ZUMlgYhD46jpk' (length=77)
              1 => 
                object(Vinelab\Youtube\Video)[803]
                  public 'kind' => string 'youtube#playlistItem' (length=20)
                  public 'id' => string 'PLoqlw2-AY6NMK_3wSxsZieCWxHwDW_yyZU5fw7We9j94' (length=45) ...
              2 => 
              ...
```

#### `Youtube::sync($resource)`

> To sync any retrieved resource use `Youtube::sync($resource)`.


* You can sync ```Video|Channel|Playlist``` and it will return the changed object ```Video|Channel|Playlist```.
* if the saved data was changed manually, you won't be able to sync the data, this means that the ```sync_enabled``` 
value is set to ```false```.
* if a video and a channel were passed to the sync method, or an empty(deleted) channel or video
an ```IncompatibleParametersObjectTypesException``` will be thrown.

#### URL Validation
* To validate the given URLs, you can use use ```Vinelab\Youtube\Validators\VideoValidator``` Class.
* Use the ```validate``` method and pass the urls to it.

Example:

```php
$this->validator->validate(compact('url', 'url1', 'url2', 'url3'));
```
If the validation failes, an ```InvalidVideoException``` will be thrown.


##### Package dependacies:
* [vinelab/http](https://github.com/Vinelab/http)

