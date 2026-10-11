<?php

declare(strict_types=1);

namespace App\Domain\Competition\Events;

use App\Domain\Competition\Models\Competition;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompetitionStartingSoon
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Competition $competition,
    ) {}
}
