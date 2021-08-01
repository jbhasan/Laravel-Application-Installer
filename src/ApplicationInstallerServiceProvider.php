<?php

namespace Sayeed\ApplicationInstaller;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Sayeed\ApplicationInstaller\Http\Middleware\IsInstalled;

class ApplicationInstallerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
		$kernel = $this->app->make(Kernel::class);
		$kernel->pushMiddleware(IsInstalled::class);

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
