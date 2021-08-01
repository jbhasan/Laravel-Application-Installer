<?php

namespace Sayeed\ApplicationInstaller\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $middleware = [
		\Sayeed\ApplicationInstaller\Http\Middleware\IsInstalled::class
    ];
}
