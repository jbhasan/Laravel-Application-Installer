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
			$pathinfo = pathinfo($request->path());
			if (isset($pathinfo['extension']) && in_array($pathinfo['extension'], ['css', 'js', 'jpg', 'jpeg', 'png'])) {
				return $next($request);
			}
			if (in_array($request->path(), ['install', 'install/check-requirements', 'install/check-connection', 'install/process', 'install/migrate', 'install/check-smtp-connection'])) {
				return $next($request);
			}
			return redirect(url('/') . '/install');
		}

		return $next($request);
	}
}