<?php

declare(strict_types=1);

namespace App\Domain\Sale\Models;

use App\Domain\Competition\Models\Competition;
use App\Domain\Customer\Models\Customer;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TicketSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'competition_id',
        'ticket_id',
        'customer_id',
        'master_id',
        'cashier_id',
        'ticket_price',
        'cashier_commission',
        'master_commission',
        'gross_amount',
        'net_amount',
        'status',
        'sold_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'ticket_price' => 'integer',
        'cashier_commission' => 'integer',
        'master_commission' => 'integer',
        'gross_amount' => 'integer',
        'net_amount' => 'integer',
        'sold_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $sale): void {
            $sale->uuid ??= (string) Str::uuid();
        });
    }

    /** @return BelongsTo<Competition, $this> */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /** @return BelongsTo<Ticket, $this> */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<User, $this> */
    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    /** @return BelongsTo<User, $this> */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /** @return BelongsTo<User, $this> */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
