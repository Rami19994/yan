<?php

declare(strict_types=1);

namespace App\Domain\Competition\Enums;

enum CompetitionStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Active = 'active';
    case SalesClosed = 'sales_closed';
    case Drawing = 'drawing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Active => 'Active',
            self::SalesClosed => 'Sales Closed',
            self::Drawing => 'Drawing',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function isSellable(): bool
    {
        return $this === self::Active;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled], true);
    }

    /**
     * @return list<self>
     */
    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::Draft => [self::Scheduled, self::Active, self::Cancelled],
            self::Scheduled => [self::Active, self::Cancelled],
            self::Active => [self::SalesClosed, self::Cancelled],
            self::SalesClosed => [self::Drawing, self::Active, self::Cancelled],
            self::Drawing => [self::Completed, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNextStatuses(), true);
    }
}
