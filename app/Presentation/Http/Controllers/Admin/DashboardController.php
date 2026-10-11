<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Application\Queries\Dashboard\GetAdminDashboardStatsQuery;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(GetAdminDashboardStatsQuery $query): View
    {
        return view('admin.dashboard', ['stats' => $query->execute()]);
    }
}
