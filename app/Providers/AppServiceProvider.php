<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('APP_ENV') !== 'local' && env('URL_SCHEME')) {
            URL::forceScheme('https');
        }

        foreach(File::directories(app_path('Modules')) as $moduleDir) {
            View::addNamespace(basename($moduleDir), $moduleDir.'/Views');
        }
    }
}
