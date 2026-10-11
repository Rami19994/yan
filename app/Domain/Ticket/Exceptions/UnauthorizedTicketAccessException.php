<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Exceptions;

use App\Shared\Exceptions\DomainException;

final class UnauthorizedTicketAccessException extends DomainException
{
    public static function create(): self
    {
        return new self('You are not authorized to access this ticket.');
    }
}
