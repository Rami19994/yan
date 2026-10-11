<?php

declare(strict_types=1);

namespace App\Domain\Competition\Exceptions;

use App\Domain\Competition\Enums\CompetitionStatus;
use App\Shared\Exceptions\DomainException;

final class InvalidCompetitionStatusTransitionException extends DomainException
{
    public static function from(CompetitionStatus $from, CompetitionStatus $to): self
    {
        return new self("Cannot transition competition from \"{$from->value}\" to \"{$to->value}\".");
    }
}
