<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Public;

use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyTicketController extends Controller
{
    public function __invoke(Request $request, string $token): View
    {
        $ticket = Ticket::query()
            ->where('public_token', $token)
            ->with(['competition', 'customer'])
            ->first();

        if (! $ticket) {
            return view('public.verify-not-found');
        }

        $isSold = in_array($ticket->status, [TicketStatus::Sold, TicketStatus::Winner, TicketStatus::PrizePaid], true);

        return view('public.verify', [
            'ticket' => $ticket,
            'isSold' => $isSold,
            'maskedCustomerName' => $isSold ? $ticket->customer?->maskedName() : null,
            'maskedCustomerPhone' => $isSold ? $ticket->customer?->maskedPhone() : null,
        ]);
    }
}
