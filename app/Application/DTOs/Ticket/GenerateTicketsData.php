<?php

declare(strict_types=1);

namespace App\Application\DTOs\Ticket;

final readonly class GenerateTicketsData
{
    public function __construct(
        public int $competitionId,
        public int $count,
    ) {}
}
