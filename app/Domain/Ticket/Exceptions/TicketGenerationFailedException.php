<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Exceptions;

use App\Shared\Exceptions\DomainException;

final class TicketGenerationFailedException extends DomainException
{
    public static function unableToAllocate(int $remaining): self
    {
        return new self("Unable to allocate {$remaining} unique ticket number(s) after multiple attempts.");
    }
}
