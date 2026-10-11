<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Master;

use App\Application\Queries\Dashboard\GetMasterDashboardStatsQuery;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, GetMasterDashboardStatsQuery $query): View
    {
        return view('master.dashboard', $query->execute($request->user()->id));
    }
}
