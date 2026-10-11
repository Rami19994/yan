<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\UpdateUserData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class UpdateUserUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(User $target, UpdateUserData $data): User
    {
        return DB::transaction(function () use ($target, $data) {
            $old = $target->only(['name', 'email', 'phone']);

            $attributes = [
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
            ];

            if ($data->password) {
                $attributes['password'] = Hash::make($data->password);
            }

            $target->update($attributes);

            $this->auditLogger->log('User Updated', $target, $old, [
                'name' => $target->name,
                'email' => $target->email,
                'phone' => $target->phone,
            ]);

            return $target;
        });
    }
}
