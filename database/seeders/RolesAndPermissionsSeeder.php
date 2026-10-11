<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\User\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    public const array PERMISSIONS = [
        // Users / hierarchy
        'masters.manage',
        'cashiers.manage',
        'users.view-all',

        // Competitions
        'competitions.manage',
        'competitions.view',

        // Tickets
        'tickets.generate',
        'tickets.assign-to-master',
        'tickets.assign-to-cashier',
        'tickets.sell',
        'tickets.view-all',
        'tickets.cancel',

        // Customers
        'customers.manage',

        // Draw
        'draw.control',
        'draw.select-winner',

        // Reports & audit
        'reports.view',
        'audit-logs.view',

        // Settings
        'settings.manage',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => UserRole::Admin->value]);
        $admin->syncPermissions(self::PERMISSIONS);

        $master = Role::firstOrCreate(['name' => UserRole::Master->value]);
        $master->syncPermissions([
            'cashiers.manage',
            'competitions.view',
            'tickets.assign-to-cashier',
            'tickets.view-all',
            'customers.manage',
            'reports.view',
        ]);

        $cashier = Role::firstOrCreate(['name' => UserRole::Cashier->value]);
        $cashier->syncPermissions([
            'competitions.view',
            'tickets.sell',
            'customers.manage',
            'reports.view',
        ]);
    }
}
