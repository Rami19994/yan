<?php

declare(strict_types=1);

namespace App\Shared\ValueObjects;

use InvalidArgumentException;

final readonly class TicketNumber
{
    public const int MIN = 1_000_000;

    public const int MAX = 9_999_999;

    private function __construct(
        public int $value,
    ) {
        if ($this->value < self::MIN || $this->value > self::MAX) {
            throw new InvalidArgumentException(
                sprintf('Ticket number must be between %d and %d.', self::MIN, self::MAX)
            );
        }
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public static function generateRandom(): self
    {
        return new self(random_int(self::MIN, self::MAX));
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }
}
