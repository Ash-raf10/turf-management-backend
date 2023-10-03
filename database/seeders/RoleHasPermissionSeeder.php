<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'admin')->first();
        $allPermissions = Permission::all();

        if ($role) {
            $role->syncPermissions($allPermissions);
        }
    }
}
