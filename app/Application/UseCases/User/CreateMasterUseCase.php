<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\CreateMasterData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class CreateMasterUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(CreateMasterData $data, User $admin): User
    {
        return DB::transaction(function () use ($data, $admin) {
            $master = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'password' => Hash::make($data->password),
                'created_by' => $admin->id,
                'is_active' => true,
            ]);

            $master->assignRole(UserRole::Master->value);

            $this->auditLogger->log('Master Created', $master, [], [
                'name' => $master->name,
                'email' => $master->email,
            ]);

            return $master;
        });
    }
}
