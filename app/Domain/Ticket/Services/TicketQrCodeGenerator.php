<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Services;

use App\Domain\Ticket\Models\Ticket;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

/**
 * The QR code always encodes the ticket's secure public_token (a random
 * UUID), never the ticket number — the ticket number is predictable/
 * sequential-ish and short, so using it directly in a public URL would let
 * anyone probe other tickets by guessing numbers.
 */
class TicketQrCodeGenerator
{
    public function verifyUrl(Ticket $ticket): string
    {
        return route('verify.show', ['token' => $ticket->public_token]);
    }

    public function dataUri(Ticket $ticket, int $size = 300): string
    {
        $result = (new Builder(
            writer: new PngWriter,
            data: $this->verifyUrl($ticket),
            size: $size,
            margin: 10,
        ))->build();

        return $result->getDataUri();
    }
}
