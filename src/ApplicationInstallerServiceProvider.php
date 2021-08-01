<?php

namespace Sayeed\ApplicationInstaller;

use Illuminate\Support\ServiceProvider;

class ApplicationInstallerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
		$this->loadViewsFrom(__DIR__ . '/resources/views', 'application_installer');
		$this->publishes([
			__DIR__ . '/public' => public_path('vendor/sayeed/application_installer'),
		]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
		//
    }
}
