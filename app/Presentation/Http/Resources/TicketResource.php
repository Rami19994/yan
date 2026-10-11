<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use App\Domain\Ticket\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ticket
 */
class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'ticket_number' => $this->ticket_number,
            'status' => $this->status->value,
            'competition_id' => $this->competition_id,
            'master_id' => $this->master_id,
            'cashier_id' => $this->cashier_id,
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'phone' => $this->customer->phone,
            ]),
            'price_at_sale' => $this->price_at_sale,
            'sold_at' => $this->sold_at?->toIso8601String(),
            'is_winner' => $this->is_winner,
        ];
    }
}
