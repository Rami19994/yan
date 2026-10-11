<?php

declare(strict_types=1);

namespace App\Domain\Draw\Exceptions;

use App\Shared\Exceptions\DomainException;

final class DrawAlreadyStartedException extends DomainException
{
    public static function create(): self
    {
        return new self('A draw has already been started or completed for this competition.');
    }
}
