<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Admin;

use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CashiersController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $cashiers = User::query()
            ->role(UserRole::Cashier->value)
            ->with('master')
            ->orderBy('name')
            ->paginate(25);

        return UserResource::collection($cashiers);
    }
}
