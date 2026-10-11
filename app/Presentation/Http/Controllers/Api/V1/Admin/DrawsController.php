<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Admin;

use App\Domain\Draw\Models\Draw;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\DrawResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DrawsController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $draws = Draw::query()
            ->orderByDesc('created_at')
            ->paginate(25);

        return DrawResource::collection($draws);
    }
}
