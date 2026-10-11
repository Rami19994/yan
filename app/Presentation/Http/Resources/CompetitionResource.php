<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use App\Domain\Competition\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Competition
 */
class CompetitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status->value,
            'ticket_price' => $this->ticket_price,
            'prize_amount' => $this->prize_amount,
            'draw_mode' => $this->draw_mode->value,
            'draw_datetime' => $this->draw_datetime?->toIso8601String(),
            'sales_open_datetime' => $this->sales_open_datetime?->toIso8601String(),
            'sales_close_datetime' => $this->sales_close_datetime?->toIso8601String(),
        ];
    }
}
