<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\Ticket\Models\Ticket;
use App\Domain\Ticket\Services\TicketQrCodeGenerator;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class TicketQrCodeController extends Controller
{
    public function __invoke(Ticket $ticket, TicketQrCodeGenerator $generator): View
    {
        Gate::authorize('view', $ticket);

        return view('admin.tickets.qr-code', [
            'ticket' => $ticket,
            'dataUri' => $generator->dataUri($ticket),
            'verifyUrl' => $generator->verifyUrl($ticket),
        ]);
    }
}
