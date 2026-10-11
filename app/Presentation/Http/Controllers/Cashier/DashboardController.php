<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Cashier;

use App\Application\Queries\Dashboard\GetCashierDashboardStatsQuery;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, GetCashierDashboardStatsQuery $query): View
    {
        return view('cashier.dashboard', $query->execute($request->user()->id));
    }
}
