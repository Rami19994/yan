<?php

declare(strict_types=1);

namespace App\Domain\User\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Master = 'master';
    case Cashier = 'cashier';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Master => 'Master',
            self::Cashier => 'Cashier',
        };
    }
}
