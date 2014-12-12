<?php namespace Vinelab\Youtube;

use App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Vinelab\Youtube\Contracts\ApiInterface;
use Vinelab\Youtube\Validators\VideoValidator;
use Vinelab\Youtube\Validators\VideoResponseValidator;
use Vinelab\Youtube\Validators\SearchResponseValidator;
use Vinelab\Youtube\Validators\ChannelResponseValidator;

class YoutubeServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Vinelab\Youtube\Contracts\ApiInterface', function () {
            return new Api(App::make('config'),
                           App::make('Vinelab\Http\Client'),
                           App::make('Vinelab\Youtube\Contracts\VideoInterface'),
                           App::make('Vinelab\Youtube\Contracts\ParserInterface'),
                           App::make('Vinelab\Youtube\Validators\VideoResponseValidator'),
                           App::make('Vinelab\Youtube\Validators\ChannelResponseValidator'),
                           App::make('Vinelab\Youtube\Validators\SearchResponseValidator'));
        });

        $this->app->bind('Vinelab\Youtube\Validators\VideoValidator', function () {
            return new VideoValidator($this->app->make('validator'));
        });

        $this->app->bind('Vinelab\Youtube\Validators\VideoResponseValidator', function () {
            return new VideoResponseValidator($this->app->make('validator'));
        });

        $this->app->bind('Vinelab\Youtube\Contracts\VideoInterface', 'Vinelab\Youtube\Video');

        $this->app->bind('Vinelab\Youtube\Contracts\ChannelInterface', 'Vinelab\Youtube\Channel');

        $this->app->bind('Vinelab\Youtube\Contracts\ParserInterface', 'Vinelab\Youtube\Parser');

        $this->app->bind('Vinelab\Youtube\Contracts\SynchronizerInterface', 'Vinelab\Youtube\Synchronizer');

        $this->app->bind('Vinelab\Youtube\Contracts\ParserInterface', function () {
            return new Parser(App::make('Vinelab\Youtube\Contracts\VideoInterface'),
                              App::make('Vinelab\Youtube\Contracts\ChannelInterface'));
        });

        $this->app->bind('Vinelab\Youtube\Contracts\ManagerInterface', function () {
            return new Manager(
                App::make('Vinelab\Youtube\Contracts\ApiInterface'),
                App::make('Vinelab\Youtube\Contracts\SynchronizerInterface'));
        });

        $this->app->bind('Vinelab\Youtube\Contracts\YoutubeInterface', function () {
            return new Youtube(App::make('Vinelab\Youtube\Contracts\ManagerInterface'));
        });
    }

    public function boot()
    {
        $this->package('vinelab/youtube', 'Vinelab\Youtube');

        $this->app->register('Vinelab\Http\HttpServiceProvider');

        AliasLoader::getInstance()->alias('Youtube', 'Vinelab\Youtube\Facades\Youtube');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
