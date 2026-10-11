<?php

declare(strict_types=1);

namespace App\Domain\User\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'password',
        'master_id',
        'created_by',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            $user->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * The Master this Cashier belongs to (null for Admin/Master accounts).
     */
    public function master(): BelongsTo
    {
        return $this->belongsTo(self::class, 'master_id');
    }

    public function cashiers(): HasMany
    {
        return $this->hasMany(self::class, 'master_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'cashier_id');
    }

    public function ticketsAsMaster(): HasMany
    {
        return $this->hasMany(Ticket::class, 'master_id');
    }

    public function ticketsAsCashier(): HasMany
    {
        return $this->hasMany(Ticket::class, 'cashier_id');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin->value);
    }

    public function isMaster(): bool
    {
        return $this->hasRole(UserRole::Master->value);
    }

    public function isCashier(): bool
    {
        return $this->hasRole(UserRole::Cashier->value);
    }
}
