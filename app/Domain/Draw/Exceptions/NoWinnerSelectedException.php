<?php

declare(strict_types=1);

namespace App\Domain\Draw\Exceptions;

use App\Shared\Exceptions\DomainException;

final class NoWinnerSelectedException extends DomainException
{
    public static function create(): self
    {
        return new self('This competition is set to Admin Selected mode but no winning ticket has been selected yet.');
    }
}
