<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Models;

use App\Domain\Assignment\Enums\AssignmentType;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAssignment extends Model
{
    public const ?string UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'from_user_id',
        'to_user_id',
        'assignment_type',
        'assigned_by',
    ];

    protected $casts = [
        'assignment_type' => AssignmentType::class,
        'created_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
