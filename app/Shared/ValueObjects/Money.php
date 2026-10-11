<?php

declare(strict_types=1);

namespace App\Shared\ValueObjects;

use InvalidArgumentException;

/**
 * Represents a virtual monetary amount stored as whole IQD units (no fractions, no floats).
 * All amounts in this system are simulation values only — no real currency is moved.
 */
final readonly class Money
{
    private function __construct(
        public int $amount,
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative.');
        }
    }

    public static function fromInt(int $amount): self
    {
        return new self($amount);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function subtract(self $other): self
    {
        return new self($this->amount - $other->amount);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount;
    }

    public function toInt(): int
    {
        return $this->amount;
    }

    public function format(): string
    {
        return number_format($this->amount).' IQD';
    }
}
