<?php

declare(strict_types=1);

namespace App\Application\DTOs\Assignment;

final readonly class AssignTicketsToMasterData
{
    public function __construct(
        public int $competitionId,
        public int $masterId,
        public int $count,
    ) {}
}
