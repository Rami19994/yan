<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Assignment\Events\TicketsAssigned;
use App\Domain\Audit\Policies\AuditLogPolicy;
use App\Domain\Competition\Events\CompetitionStartingSoon;
use App\Domain\Competition\Models\Competition;
use App\Domain\Competition\Policies\CompetitionPolicy;
use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Policies\CustomerPolicy;
use App\Domain\Draw\Events\DrawCompleted;
use App\Domain\Draw\Models\Draw;
use App\Domain\Draw\Policies\DrawPolicy;
use App\Domain\Settings\Policies\SettingPolicy;
use App\Domain\Ticket\Events\TicketSold;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\Ticket\Policies\TicketPolicy;
use App\Domain\User\Events\CashierCreated;
use App\Domain\User\Models\User;
use App\Domain\User\Policies\UserPolicy;
use App\Domain\Winner\Events\WinnerSelected;
use App\Domain\Winner\Models\Winner;
use App\Domain\Winner\Policies\WinnerPolicy;
use App\Infrastructure\Listeners\LogTicketSoldListener;
use App\Infrastructure\Listeners\NotifyAdminsOfCashierCreated;
use App\Infrastructure\Listeners\NotifyAdminsOfDrawCompleted;
use App\Infrastructure\Listeners\NotifyAdminsOfWinnerSelected;
use App\Infrastructure\Listeners\NotifyMasterOfTicketSold;
use App\Infrastructure\Listeners\NotifyOfCompetitionStartingSoon;
use App\Infrastructure\Listeners\NotifyRecipientOfTicketsAssigned;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'Database\\Factories\\'.Str::afterLast($modelName, '\\').'Factory'
        );

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Competition::class, CompetitionPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Draw::class, DrawPolicy::class);
        Gate::policy(Winner::class, WinnerPolicy::class);

        Gate::define('audit-logs.view', [AuditLogPolicy::class, 'viewAny']);
        Gate::define('settings.manage', [SettingPolicy::class, 'manage']);

        Event::listen(TicketSold::class, LogTicketSoldListener::class);
        Event::listen(TicketSold::class, NotifyMasterOfTicketSold::class);
        Event::listen(CashierCreated::class, NotifyAdminsOfCashierCreated::class);
        Event::listen(TicketsAssigned::class, NotifyRecipientOfTicketsAssigned::class);
        Event::listen(DrawCompleted::class, NotifyAdminsOfDrawCompleted::class);
        Event::listen(WinnerSelected::class, NotifyAdminsOfWinnerSelected::class);
        Event::listen(CompetitionStartingSoon::class, NotifyOfCompetitionStartingSoon::class);

        // Same per-email+IP throttle as the web login form (LoginRequest),
        // applied to the API login endpoint — it had no rate limiting at
        // all before this, unlike its web counterpart.
        RateLimiter::for('api-login', function ($request) {
            $key = Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());

            return Limit::perMinute(5)->by($key);
        });
    }
}
