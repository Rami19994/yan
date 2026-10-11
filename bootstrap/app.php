<?php

use App\Infrastructure\Console\Commands\CloseExpiredCompetitionSales;
use App\Infrastructure\Console\Commands\NotifyCompetitionsStartingSoon;
use App\Presentation\Http\Middleware\ApplyDynamicSettings;
use App\Presentation\Http\Middleware\CheckMaintenanceMode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withCommands([
        CloseExpiredCompetitionSales::class,
        NotifyCompetitionsStartingSoon::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web(append: [
            ApplyDynamicSettings::class,
            CheckMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Lets the app's public/web directory live somewhere other than ./public —
// needed on shared hosts with no SSH access, where only ONE folder can be
// the domain's web root and the rest of the Laravel app has to sit outside
// it. Unset in local/normal deployments, so this has no effect there.
// Given as a path relative to this app's own folder (e.g. "../htdocs")
// rather than an absolute server path, since the deployer rarely knows
// their account's absolute filesystem path in advance.
if ($publicPath = env('APP_PUBLIC_PATH')) {
    $resolved = realpath(dirname(__DIR__).'/'.$publicPath) ?: dirname(__DIR__).'/'.$publicPath;
    $app->usePublicPath($resolved);
}

return $app;
