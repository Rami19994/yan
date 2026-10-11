<?php

declare(strict_types=1);

namespace App\Application\DTOs\Draw;

final readonly class SelectWinnerData
{
    public function __construct(
        public int $competitionId,
        public int $ticketId,
    ) {}
}
