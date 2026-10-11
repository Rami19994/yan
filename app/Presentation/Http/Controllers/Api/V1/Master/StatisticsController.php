<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Master;

use App\Application\Queries\Dashboard\GetMasterDashboardStatsQuery;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __invoke(Request $request, GetMasterDashboardStatsQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->execute($request->user()->id)]);
    }
}
