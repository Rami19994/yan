<?php

declare(strict_types=1);

namespace App\Domain\Draw\Models;

use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Models\Competition;
use App\Domain\Draw\Enums\DrawStatus;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Draw extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'competition_id',
        'draw_mode',
        'status',
        'is_preview',
        'started_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'draw_mode' => DrawMode::class,
        'status' => DrawStatus::class,
        'is_preview' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $draw): void {
            $draw->uuid ??= (string) Str::uuid();
        });
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(DrawAttempt::class);
    }
}
