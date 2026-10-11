<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use App\Domain\Draw\Models\Draw;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Draw
 */
class DrawResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'competition_id' => $this->competition_id,
            'draw_mode' => $this->draw_mode->value,
            'status' => $this->status->value,
            'is_preview' => $this->is_preview,
            'started_by' => $this->started_by,
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }
}
