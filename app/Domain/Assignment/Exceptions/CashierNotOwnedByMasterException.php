<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Exceptions;

use App\Shared\Exceptions\DomainException;

final class CashierNotOwnedByMasterException extends DomainException
{
    public static function create(): self
    {
        return new self('This cashier does not belong to you.');
    }
}
