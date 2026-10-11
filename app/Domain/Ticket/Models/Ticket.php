<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Models;

use App\Domain\Assignment\Models\TicketAssignment;
use App\Domain\Competition\Models\Competition;
use App\Domain\Customer\Models\Customer;
use App\Domain\Sale\Models\TicketSale;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'public_token',
        'competition_id',
        'ticket_number',
        'master_id',
        'cashier_id',
        'customer_id',
        'status',
        'price_at_sale',
        'cashier_commission_at_sale',
        'master_commission_at_sale',
        'assigned_to_master_at',
        'assigned_to_cashier_at',
        'sold_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'is_winner',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'ticket_number' => 'integer',
        'price_at_sale' => 'integer',
        'cashier_commission_at_sale' => 'integer',
        'master_commission_at_sale' => 'integer',
        'assigned_to_master_at' => 'datetime',
        'assigned_to_cashier_at' => 'datetime',
        'sold_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_winner' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket): void {
            $ticket->uuid ??= (string) Str::uuid();
            $ticket->public_token ??= (string) Str::uuid();
        });
    }

    /** @return BelongsTo<Competition, $this> */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
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

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<User, $this> */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function sale(): HasMany
    {
        return $this->hasMany(TicketSale::class);
    }
}
