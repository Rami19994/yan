<?php

declare(strict_types=1);

namespace App\Application\DTOs\User;

final readonly class CreateMasterData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $password,
    ) {}
}
