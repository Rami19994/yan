<?php

declare(strict_types=1);

namespace App\Domain\Winner\Enums;

enum WinnerStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case VirtualPrizeDelivered = 'virtual_prize_delivered';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::VirtualPrizeDelivered => 'Virtual Prize Delivered',
        };
    }

    /**
     * @return list<self>
     */
    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed],
            self::Confirmed => [self::VirtualPrizeDelivered],
            self::VirtualPrizeDelivered => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNextStatuses(), true);
    }
}
