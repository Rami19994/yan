<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Exceptions;

use App\Shared\Exceptions\DomainException;

final class TicketNotAssignedException extends DomainException
{
    public static function toCashier(int $ticketNumber): self
    {
        return new self("Ticket #{$ticketNumber} is not assigned to this cashier.");
    }

    public static function toMaster(int $ticketNumber): self
    {
        return new self("Ticket #{$ticketNumber} is not assigned to this master.");
    }
}
