<?php

namespace Hsk\TarsHelper;

use Hsk\TarsHelper\Commands\Tars;

class TarsHelperProvider extends \Lxj\Laravel\Tars\ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        parent::registerCommands();
        $this->commands([Tars::class]);

        $this->app->singleton('tars-helper', function($app){
            return new TarsHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();

        $this->publishes([
            __DIR__ . '/config/tars-helper.php' => config_path('tars-helper.php'),
        ],'tars-helper');
    }
}
