<?php

declare(strict_types=1);

namespace App\Application\DTOs\Assignment;

final readonly class AssignTicketsToCashierData
{
    public function __construct(
        public int $competitionId,
        public int $cashierId,
        public int $count,
    ) {}
}
