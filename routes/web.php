<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Presentation\Http\Controllers\Admin\TicketQrCodeController;
use App\Presentation\Http\Controllers\Auth\LoginController;
use App\Presentation\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Presentation\Http\Controllers\Master\DashboardController as MasterDashboardController;
use App\Presentation\Http\Controllers\Public\LiveDrawController;
use App\Presentation\Http\Controllers\Public\VerifyTicketController;
use App\Presentation\Livewire\Admin\AssignTicketsToMaster;
use App\Presentation\Livewire\Admin\AuditLogsIndex;
use App\Presentation\Livewire\Admin\CashiersIndex as AdminCashiersIndex;
use App\Presentation\Livewire\Admin\CompetitionForm;
use App\Presentation\Livewire\Admin\CompetitionsIndex;
use App\Presentation\Livewire\Admin\DrawControl;
use App\Presentation\Livewire\Admin\MastersIndex;
use App\Presentation\Livewire\Admin\SettingsForm;
use App\Presentation\Livewire\Admin\TicketsGenerate;
use App\Presentation\Livewire\Admin\WinnerSelection;
use App\Presentation\Livewire\Admin\WinnersIndex;
use App\Presentation\Livewire\Cashier\SellTicket;
use App\Presentation\Livewire\Cashier\WinnersIndex as CashierWinnersIndex;
use App\Presentation\Livewire\Master\AssignTicketsToCashier;
use App\Presentation\Livewire\Master\CashiersIndex;
use App\Presentation\Livewire\Shared\NotificationsIndex;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/draw/live/{competition}', LiveDrawController::class)->name('draw.live');
Route::get('/verify/{token}', VerifyTicketController::class)->name('verify.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware('auth')->get('/notifications', NotificationsIndex::class)->name('notifications.index');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/masters', MastersIndex::class)->name('masters.index');
    Route::get('/cashiers', AdminCashiersIndex::class)->name('cashiers.index');

    Route::get('/competitions', CompetitionsIndex::class)->name('competitions.index');
    Route::get('/competitions/create', CompetitionForm::class)->name('competitions.create');
    Route::get('/competitions/{competition}/edit', CompetitionForm::class)->name('competitions.edit');

    Route::get('/tickets/generate', TicketsGenerate::class)->name('tickets.generate');
    Route::get('/tickets/assign', AssignTicketsToMaster::class)->name('tickets.assign');

    Route::get('/competitions/{competition}/winner', WinnerSelection::class)->name('competitions.winner');
    Route::get('/competitions/{competition}/draw', DrawControl::class)->name('competitions.draw');

    Route::get('/winners', WinnersIndex::class)->name('winners.index');

    Route::get('/tickets/{ticket}/qr-code', TicketQrCodeController::class)->name('tickets.qr-code');

    Route::get('/audit-logs', AuditLogsIndex::class)->name('audit-logs.index');

    Route::get('/settings', SettingsForm::class)->name('settings.index');
});

Route::middleware(['auth', 'role:master'])->prefix('master')->name('master.')->group(function () {
    Route::get('/dashboard', MasterDashboardController::class)->name('dashboard');
    Route::get('/cashiers', CashiersIndex::class)->name('cashiers.index');
    Route::get('/tickets/assign', AssignTicketsToCashier::class)->name('tickets.assign');
});

Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard', CashierDashboardController::class)->name('dashboard');
    Route::get('/sell', SellTicket::class)->name('tickets.sell');
    Route::get('/winners', CashierWinnersIndex::class)->name('winners.index');
});

Route::get('/run-migrations', function (\Illuminate\Http\Request $request) {
    if ($request->query('secret') !== env('MIGRATE_SECRET', 'yanasib2026')) {
        abort(403, 'Unauthorized: invalid or missing secret token.');
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();

        if ($request->has('seed')) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $output .= "\n" . \Illuminate\Support\Facades\Artisan::output();
        }

        return response("<pre style='background:#18181b;color:#4ade80;padding:20px;border-radius:8px;font-family:monospace;line-height:1.5;'>{$output}\nMigration / Seed process completed successfully!</pre>");
    } catch (\Throwable $e) {
        return response("<pre style='background:#18181b;color:#f87171;padding:20px;border-radius:8px;font-family:monospace;line-height:1.5;'>Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>", 500);
    }
});

