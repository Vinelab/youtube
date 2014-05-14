<?php namespace Vinelab\Youtube\Tests;

class TestCase extends \Orchestra\Testbench\TestCase {

    protected function getPackageProviders()
    {
        return array('Vinelab\Youtube\YoutubeServiceProvider');
    }
}