<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo data seeder for the Yanasib simulation platform.
 * Populated incrementally as each domain (competitions, tickets, sales) comes online.
 */
class YanasibDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@yanasib.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $master1 = User::factory()->create([
            'name' => 'Master One',
            'email' => 'master1@yanasib.test',
            'password' => bcrypt('password'),
            'created_by' => $admin->id,
            'is_active' => true,
        ]);
        $master1->assignRole(UserRole::Master->value);

        $master2 = User::factory()->create([
            'name' => 'Master Two',
            'email' => 'master2@yanasib.test',
            'password' => bcrypt('password'),
            'created_by' => $admin->id,
            'is_active' => true,
        ]);
        $master2->assignRole(UserRole::Master->value);

        foreach ([$master1, $master2] as $i => $master) {
            for ($c = 1; $c <= 2; $c++) {
                $cashier = User::factory()->create([
                    'name' => "Cashier {$i}-{$c}",
                    'email' => "cashier{$i}{$c}@yanasib.test",
                    'password' => bcrypt('password'),
                    'master_id' => $master->id,
                    'created_by' => $master->id,
                    'is_active' => true,
                ]);
                $cashier->assignRole(UserRole::Cashier->value);
            }
        }
    }
}
