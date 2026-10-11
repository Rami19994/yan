<?php

declare(strict_types=1);

namespace App\Domain\Competition\Exceptions;

use App\Shared\Exceptions\DomainException;

final class CompetitionClosedException extends DomainException
{
    public static function salesNotOpen(): self
    {
        return new self('Sales are not currently open for this competition.');
    }

    public static function notActive(): self
    {
        return new self('This competition is not active.');
    }
}
