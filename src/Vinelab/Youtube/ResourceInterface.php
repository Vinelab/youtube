<?php namespace Vinelab\Youtube;

interface ResourceInterface {
    /**
     * Return raw youtube information
     * @return array
     */
    public function getYoutubeInfo();

    /**
     * Return the youtube synced_at value
     * @return date 
     */
    public function youtubeSyncedAt();
}