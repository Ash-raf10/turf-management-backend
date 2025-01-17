<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RoleHasPermissionSeeder::class,
        ]);
    }
}
