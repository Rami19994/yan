<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Exceptions;

use App\Shared\Exceptions\DomainException;

final class NotEnoughTicketsAvailableException extends DomainException
{
    public static function create(int $requested, int $available): self
    {
        return new self("Requested {$requested} ticket(s) but only {$available} are available.");
    }
}
