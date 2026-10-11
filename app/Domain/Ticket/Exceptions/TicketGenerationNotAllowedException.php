<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Exceptions;

use App\Shared\Exceptions\DomainException;

final class TicketGenerationNotAllowedException extends DomainException
{
    public static function competitionIsTerminal(): self
    {
        return new self('Tickets cannot be generated for a cancelled or completed competition.');
    }
}
