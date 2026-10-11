<?php

declare(strict_types=1);

namespace App\Application\DTOs\Sale;

final readonly class SellTicketData
{
    public function __construct(
        public int $ticketId,
        public string $customerName,
        public string $customerPhone,
    ) {}
}
