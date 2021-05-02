<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/21/20, 9:10 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(iwx_path('config/iwx.php'), 'ilaravel.wx');

        if($this->app->runningInConsole())
        {
            if (iwx('database.migrations.include', true)) $this->loadMigrationsFrom(iwx_path('database/migrations'));
        }

    }

    public function register()
    {
        parent::register();
    }
}
