<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\Api\V1\Admin\CashiersController as AdminCashiersController;
use App\Presentation\Http\Controllers\Api\V1\Admin\DrawsController;
use App\Presentation\Http\Controllers\Api\V1\Admin\MastersController;
use App\Presentation\Http\Controllers\Api\V1\Admin\StatisticsController as AdminStatisticsController;
use App\Presentation\Http\Controllers\Api\V1\Auth\AuthController;
use App\Presentation\Http\Controllers\Api\V1\Cashier\SalesController;
use App\Presentation\Http\Controllers\Api\V1\Cashier\SellTicketController;
use App\Presentation\Http\Controllers\Api\V1\CompetitionController;
use App\Presentation\Http\Controllers\Api\V1\Master\CashiersController as MasterCashiersController;
use App\Presentation\Http\Controllers\Api\V1\Master\StatisticsController as MasterStatisticsController;
use App\Presentation\Http\Controllers\Api\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:api-login')
    ->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    Route::get('/competitions', [CompetitionController::class, 'index'])->name('competitions.index');
    Route::get('/competitions/{competition}', [CompetitionController::class, 'show'])->name('competitions.show');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::middleware('role:cashier')->prefix('cashier')->name('cashier.')->group(function () {
        Route::post('/tickets/{ticket}/sell', SellTicketController::class)->name('tickets.sell');
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    });

    Route::middleware('role:master')->prefix('master')->name('master.')->group(function () {
        Route::get('/cashiers', [MasterCashiersController::class, 'index'])->name('cashiers.index');
        Route::get('/statistics', MasterStatisticsController::class)->name('statistics');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/statistics', AdminStatisticsController::class)->name('statistics');
        Route::get('/masters', [MastersController::class, 'index'])->name('masters.index');
        Route::get('/cashiers', [AdminCashiersController::class, 'index'])->name('cashiers.index');
        Route::get('/draws', [DrawsController::class, 'index'])->name('draws.index');
    });
});
