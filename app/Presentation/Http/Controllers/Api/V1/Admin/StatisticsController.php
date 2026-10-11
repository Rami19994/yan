<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Admin;

use App\Application\Queries\Dashboard\GetAdminDashboardStatsQuery;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function __invoke(GetAdminDashboardStatsQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->execute()]);
    }
}
