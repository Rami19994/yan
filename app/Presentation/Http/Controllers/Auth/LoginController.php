<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Auth;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\User\Models\User;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => __('This account has been deactivated.'),
            ]);
        }

        $this->auditLogger->log('Login', $user, [], [], $user->id);

        return redirect()->intended($this->dashboardRouteFor($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            $this->auditLogger->log('Logout', $user, [], [], $user->id);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function dashboardRouteFor(User $user): string
    {
        return match (true) {
            $user->isAdmin() => route('admin.dashboard'),
            $user->isMaster() => route('master.dashboard'),
            default => route('cashier.dashboard'),
        };
    }
}
