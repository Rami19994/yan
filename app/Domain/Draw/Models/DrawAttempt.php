<?php

declare(strict_types=1);

namespace App\Domain\Draw\Models;

use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrawAttempt extends Model
{
    public const ?string UPDATED_AT = null;

    protected $fillable = [
        'draw_id',
        'competition_id',
        'attempt_number',
        'draw_mode',
        'result_number',
        'selected_ticket_id',
        'is_winner',
        'winner_ticket_id',
        'started_by',
        'started_at',
        'completed_at',
        'hash',
    ];

    protected $casts = [
        'draw_mode' => DrawMode::class,
        'attempt_number' => 'integer',
        'result_number' => 'integer',
        'is_winner' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function selectedTicket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'selected_ticket_id');
    }

    public function winnerTicket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'winner_ticket_id');
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * SHA-256 integrity hash over competition_id + attempt_number + result_number + timestamp.
     */
    public static function computeHash(int $competitionId, int $attemptNumber, int $resultNumber, string $timestamp): string
    {
        return hash('sha256', $competitionId.'|'.$attemptNumber.'|'.$resultNumber.'|'.$timestamp);
    }
}
