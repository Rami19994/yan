<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\CreateCashierData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Events\CashierCreated;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class CreateCashierUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(CreateCashierData $data, User $master): User
    {
        return DB::transaction(function () use ($data, $master) {
            $cashier = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'password' => Hash::make($data->password),
                'master_id' => $master->id,
                'created_by' => $master->id,
                'is_active' => true,
            ]);

            $cashier->assignRole(UserRole::Cashier->value);

            $this->auditLogger->log('Cashier Created', $cashier, [], [
                'name' => $cashier->name,
                'email' => $cashier->email,
                'master_id' => $master->id,
            ]);

            event(new CashierCreated($cashier, $master));

            return $cashier;
        });
    }
}
