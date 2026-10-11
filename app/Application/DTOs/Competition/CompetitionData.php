<?php

declare(strict_types=1);

namespace App\Application\DTOs\Competition;

use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Enums\NoWinnerPolicy;

final readonly class CompetitionData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $ticketPrice,
        public int $cashierCommissionFixed,
        public int $masterCommissionFixed,
        public int $prizeAmount,
        public DrawMode $drawMode,
        public NoWinnerPolicy $noWinnerPolicy,
        public ?string $drawDatetime,
        public ?string $salesOpenDatetime,
        public ?string $salesCloseDatetime,
    ) {}
}
