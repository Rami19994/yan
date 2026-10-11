<?php

declare(strict_types=1);

namespace App\Domain\Winner\Exceptions;

use App\Domain\Winner\Enums\WinnerStatus;
use App\Shared\Exceptions\DomainException;

final class InvalidWinnerStatusTransitionException extends DomainException
{
    public static function from(WinnerStatus $from, WinnerStatus $to): self
    {
        return new self("Cannot transition winner prize status from \"{$from->value}\" to \"{$to->value}\".");
    }
}
