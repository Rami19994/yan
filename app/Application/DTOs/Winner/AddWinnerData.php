<?php

declare(strict_types=1);

namespace App\Application\DTOs\Winner;

final readonly class AddWinnerData
{
    public function __construct(
        public int $competitionId,
        public int $ticketId,
        public int $prizeAmount,
    ) {}
}
