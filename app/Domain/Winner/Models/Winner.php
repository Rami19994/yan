<?php

declare(strict_types=1);

namespace App\Domain\Winner\Models;

use App\Domain\Competition\Models\Competition;
use App\Domain\Customer\Models\Customer;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use App\Domain\Winner\Enums\WinnerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'customer_id',
        'ticket_id',
        'competition_id',
        'prize_amount',
        'status',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'status' => WinnerStatus::class,
        'prize_amount' => 'integer',
        'confirmed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $winner): void {
            $winner->uuid ??= (string) Str::uuid();
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
