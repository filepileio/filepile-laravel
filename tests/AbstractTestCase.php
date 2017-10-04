<?php

namespace FilePile\FilePileLaravel\Tests;

use FilePile\FilePileLaravel\Providers\FilePileLaravelServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    /**
     * @before
     */
    public function setTheApiKey()
    {
        $this->app->config->set('filepile.apiKey', 'testing123');
    }

    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return FilePileLaravelServiceProvider::class;
    }
}