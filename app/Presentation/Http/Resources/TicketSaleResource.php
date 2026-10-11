<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use App\Domain\Sale\Models\TicketSale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TicketSale
 */
class TicketSaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'ticket_id' => $this->ticket_id,
            'competition_id' => $this->competition_id,
            'customer' => $this->whenLoaded('customer', fn () => [
                'name' => $this->customer->name,
                'phone' => $this->customer->phone,
            ]),
            'ticket_price' => $this->ticket_price,
            'cashier_commission' => $this->cashier_commission,
            'master_commission' => $this->master_commission,
            'net_amount' => $this->net_amount,
            'status' => $this->status,
            // sold_at is a non-nullable column — always set once a sale exists.
            'sold_at' => $this->sold_at->toIso8601String(),
        ];
    }
}
