<?php

declare(strict_types=1);

namespace App\Domain\Draw\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Broadcast on a PUBLIC channel (no auth) so any number of spectator screens
 * — projectors, TVs, other browser tabs — can watch the exact same draw
 * animation land on the exact same result at the exact same moment,
 * without needing to be logged in. The result itself was already generated
 * and persisted server-side before this ever fires (see
 * StartOfficialDrawUseCase) — broadcasting only cues clients to animate a
 * reveal of a result that already exists, it never carries fresh randomness.
 */
class DrawResultBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public readonly int $competitionId,
        public readonly array $payload,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel("draw.{$this->competitionId}")];
    }

    public function broadcastAs(): string
    {
        return 'draw-result';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
