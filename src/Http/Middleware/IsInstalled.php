<?php

namespace Sayeed\ApplicationInstaller\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsInstalled
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$envPath = base_path('.env');
		if (!file_exists($envPath)) {
			return redirect(url('/') . '/install');
		}

		return $next($request);
	}
}