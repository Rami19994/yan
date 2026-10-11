<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Enums;

enum TicketStatus: string
{
    case Generated = 'generated';
    case AssignedToMaster = 'assigned_to_master';
    case AssignedToCashier = 'assigned_to_cashier';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case Cancelled = 'cancelled';
    case Winner = 'winner';
    case PrizePaid = 'prize_paid';

    public function label(): string
    {
        return match ($this) {
            self::Generated => 'Generated',
            self::AssignedToMaster => 'Assigned to Master',
            self::AssignedToCashier => 'Assigned to Cashier',
            self::Reserved => 'Reserved',
            self::Sold => 'Sold',
            self::Cancelled => 'Cancelled',
            self::Winner => 'Winner',
            self::PrizePaid => 'Prize Delivered',
        };
    }

    /**
     * What the public verification page should show for a ticket that
     * hasn't reached a customer yet — the internal distribution pipeline
     * (generated / assigned to master / assigned to cashier) is not public
     * information, it's just "not sold" from the outside.
     */
    public function publicLabel(): string
    {
        return match ($this) {
            self::Generated, self::AssignedToMaster, self::AssignedToCashier, self::Reserved => 'Not Sold',
            self::Sold => 'Sold',
            self::Cancelled => 'Cancelled',
            self::Winner => 'Winner',
            self::PrizePaid => 'Prize Delivered',
        };
    }

    public function isAvailableForSale(): bool
    {
        return $this === self::AssignedToCashier;
    }

    public function isAvailableForWinnerSelection(): bool
    {
        return in_array($this, [self::Sold, self::Winner], true);
    }
}
