<?php

namespace FilePile\FilePileLaravel\Providers;

use Illuminate\Support\ServiceProvider;

class FilePileLaravelServiceProvider extends ServiceProvider{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \FilePile\FilePileLaravel\Commands\FilePile::class,
                \FilePile\FilePileLaravel\Commands\FilePileList::class,
                \FilePile\FilePileLaravel\Commands\FilePileInstallPile::class,
            ]);
        }
        $this->publishes([
            __DIR__.'/../Configuration/Templates/filepile.php' => config_path('filepile.php')
        ], 'config');
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Configuration/Templates/filepile.php', 'filepile'
        );
    }

}