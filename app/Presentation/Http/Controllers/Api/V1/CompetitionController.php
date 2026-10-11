<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1;

use App\Domain\Competition\Models\Competition;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\CompetitionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompetitionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $competitions = Competition::query()
            ->orderByDesc('created_at')
            ->paginate(25);

        return CompetitionResource::collection($competitions);
    }

    public function show(Competition $competition): CompetitionResource
    {
        return new CompetitionResource($competition);
    }
}
