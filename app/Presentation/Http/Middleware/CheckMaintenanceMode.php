<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Domain\Settings\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * DB-driven maintenance mode (Setting::get('maintenance_mode')), distinct
 * from Laravel's own file-based `php artisan down`. Admins stay able to
 * log in and use the app (so they can turn it back off); everyone else
 * sees a maintenance page.
 */
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable('settings') || ! Setting::get('maintenance_mode', false)) {
            return $next($request);
        }

        $user = $request->user();

        if ($user?->isAdmin()) {
            return $next($request);
        }

        if ($request->routeIs('login') || $request->routeIs('login.store') || $request->routeIs('logout')) {
            return $next($request);
        }

        return response()->view('maintenance', status: 503);
    }
}
