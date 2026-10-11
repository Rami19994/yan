<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Exceptions;

use App\Shared\Exceptions\DomainException;

final class TicketAlreadySoldException extends DomainException
{
    public static function forTicketNumber(int $ticketNumber): self
    {
        return new self("Ticket #{$ticketNumber} has already been sold.");
    }
}
