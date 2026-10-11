<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Domain\Settings\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the admin-configurable timezone and default language on every
 * request. Deliberately NOT done in a ServiceProvider::boot() hook: that
 * runs for every artisan command too (including `migrate` itself on a
 * fresh install, before the settings table exists), which would fatal the
 * very first migration run. A request-scoped middleware only ever runs
 * once the app is actually serving HTTP traffic.
 */
class ApplyDynamicSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('settings')) {
            $timezone = Setting::get('timezone');
            if ($timezone) {
                date_default_timezone_set($timezone);
                config(['app.timezone' => $timezone]);
            }

            if (! $request->session()->has('locale')) {
                $language = Setting::get('default_language');
                if ($language) {
                    App::setLocale($language);
                }
            }
        }

        return $next($request);
    }
}
