<?php

declare(strict_types=1);

namespace App\Domain\Customer\Models;

use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'master_id',
        'cashier_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $customer): void {
            $customer->uuid ??= (string) Str::uuid();
        });
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * "Ali Hassan" -> "Ali H****": first name shown in full, last name
     * reduced to its initial plus asterisks for the rest of its length.
     * A single-word name just gets partially masked the same way.
     */
    public function maskedName(): string
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim($this->name)) ?: []));

        if ($parts === []) {
            return '';
        }

        if (count($parts) === 1) {
            return $this->maskWord($parts[0]);
        }

        $first = array_shift($parts);
        $last = array_pop($parts);

        return trim($first.' '.$this->maskWord($last));
    }

    private function maskWord(string $word): string
    {
        $length = mb_strlen($word);

        if ($length <= 1) {
            return $word;
        }

        return mb_substr($word, 0, 1).str_repeat('*', $length - 1);
    }

    public function maskedPhone(): string
    {
        $digits = preg_replace('/\D/', '', $this->phone) ?? '';
        $length = strlen($digits);

        if ($length <= 6) {
            return str_repeat('*', $length);
        }

        return substr($digits, 0, 4).str_repeat('*', $length - 8).substr($digits, -4);
    }
}
