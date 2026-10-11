<?php

declare(strict_types=1);

namespace App\Domain\Competition\Models;

use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Enums\NoWinnerPolicy;
use App\Domain\Draw\Models\Draw;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Competition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'status',
        'ticket_price',
        'cashier_commission_fixed',
        'master_commission_fixed',
        'prize_amount',
        'draw_mode',
        'no_winner_policy',
        'draw_datetime',
        'sales_open_datetime',
        'sales_close_datetime',
        'starting_soon_notified_at',
        'selected_winner_ticket_id',
        'winner_selected_by',
        'winner_selected_at',
        'draw_started_at',
        'draw_completed_at',
        'created_by',
    ];

    protected $casts = [
        'status' => CompetitionStatus::class,
        'draw_mode' => DrawMode::class,
        'no_winner_policy' => NoWinnerPolicy::class,
        'ticket_price' => 'integer',
        'cashier_commission_fixed' => 'integer',
        'master_commission_fixed' => 'integer',
        'prize_amount' => 'integer',
        'draw_datetime' => 'datetime',
        'sales_open_datetime' => 'datetime',
        'sales_close_datetime' => 'datetime',
        'starting_soon_notified_at' => 'datetime',
        'winner_selected_at' => 'datetime',
        'draw_started_at' => 'datetime',
        'draw_completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $competition): void {
            $competition->uuid ??= (string) Str::uuid();
        });
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function draws(): HasMany
    {
        return $this->hasMany(Draw::class);
    }

    public function selectedWinnerTicket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'selected_winner_ticket_id');
    }

    public function winnerSelectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_selected_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isSalesOpen(): bool
    {
        if ($this->status !== CompetitionStatus::Active) {
            return false;
        }

        $now = now();

        if ($this->sales_open_datetime && $now->lt($this->sales_open_datetime)) {
            return false;
        }

        if ($this->sales_close_datetime && $now->gte($this->sales_close_datetime)) {
            return false;
        }

        return true;
    }

    protected function netVirtualAmountPerTicket(): Attribute
    {
        return Attribute::get(fn () => $this->ticket_price
            - $this->cashier_commission_fixed
            - $this->master_commission_fixed);
    }
}
