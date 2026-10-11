<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Model events are relied upon for uuid/public_token generation (see model
    // `booted()` hooks), so WithoutModelEvents is intentionally not used here.

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            YanasibDemoSeeder::class,
        ]);
    }
}
