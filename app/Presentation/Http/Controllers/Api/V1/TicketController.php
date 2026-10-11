<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1;

use App\Domain\Ticket\Models\Ticket;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $tickets = Ticket::query()
            ->with('customer')
            ->when($user->isMaster(), fn ($q) => $q->where('master_id', $user->id))
            ->when($user->isCashier(), fn ($q) => $q->where('cashier_id', $user->id))
            ->orderByDesc('id')
            ->paginate(25);

        return TicketResource::collection($tickets);
    }

    public function show(Ticket $ticket): TicketResource
    {
        Gate::authorize('view', $ticket);

        $ticket->load('customer');

        return new TicketResource($ticket);
    }
}
