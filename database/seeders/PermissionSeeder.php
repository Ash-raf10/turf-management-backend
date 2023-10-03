<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //operation types
        $ops = ['create', 'read', 'update', 'delete'];
        $module = [
            'user' => [
                ...$ops,
                'user'
            ],
        ];


        foreach ($module as $mod => $permissions) {
            foreach ($permissions as $permission) {
                if (in_array($permission, $ops)) {
                    $permission = $permission . '-' . $mod;
                }
                Permission::firstOrCreate([
                    'name' => $permission,
                    'module' => $mod,
                    'guard_name' => "api",
                ]);
            }
        }
    }
}
