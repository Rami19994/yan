<?php

declare(strict_types=1);

namespace App\Domain\Winner\Exceptions;

use App\Shared\Exceptions\DomainException;

final class WinnerNotEligibleException extends DomainException
{
    public static function notSold(): self
    {
        return new self('Only a sold ticket from this competition can be selected as a winner.');
    }

    public static function differentCompetition(): self
    {
        return new self('The selected ticket does not belong to this competition.');
    }

    public static function cancelled(): self
    {
        return new self('A cancelled ticket cannot be selected as a winner.');
    }

    public static function drawAlreadyStarted(): self
    {
        return new self('The winning ticket cannot be changed after the official draw has started.');
    }

    public static function drawNotCompleted(): self
    {
        return new self('The draw for this competition has not been completed yet.');
    }

    public static function alreadyWinner(): self
    {
        return new self('This ticket has already been added as a winner.');
    }

    public static function wrongDrawMode(): self
    {
        return new self('A winning ticket can only be pre-selected for competitions set to Admin Selected draw mode. Change the competition\'s draw mode first.');
    }
}
