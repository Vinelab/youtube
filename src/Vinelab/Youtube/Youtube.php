<?php namespace Vinelab\Youtube;

use Vinelab\Youtube\Contracts\YoutubeInterface;
use Vinelab\Youtube\Contracts\ManagerInterface;

class Youtube implements YoutubeInterface {

    /**
     * The manager instance
     * @var Vinelab\Youtube\Contracts\ManagerInterface
     */
    protected $manager; 

    /**
     * Create a new instance of Youtube
     * @param  ManagerInterface $manager 
     */
    public function __construct(ManagerInterface $manager)
    {   
        $this->manager = $manager;
    }

    /**
     * return a single video info
     * @param  string $url 
     * @return Vinelab\Youtube\Video      
     */
    public function video($url)
    {
        return $this->manager->video($url);
    }

    /**
     * return a channel with its videos
     * @param  string $url       
     * @param  date $synced_at 
     * @return Vinelab\Youtube\Channel            
     */
    public function channel($url, $synced_at=null)
    {
        return $this->manager->videosForChannel($url, $synced_at);
    }

    /**
     * sync the resource
     * @param  Video|Channel $resource 
     * @return Video|Channel           
     */
    public function sync($resource)
    {
        return $this->manager->sync($resource);
    }

    /**
     * add http to the url if it does not exist.
     *
     * @param $url
     *
     * @return string
     */
    public function prepareUrl($url)
    {
        return $this->manager->prepareUrl($url);
    }
}
