<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Master;

use App\Domain\User\Models\User;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CashiersController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $cashiers = User::query()
            ->where('master_id', $request->user()->id)
            ->orderBy('name')
            ->paginate(25);

        return UserResource::collection($cashiers);
    }
}
