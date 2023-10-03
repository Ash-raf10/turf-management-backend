<?php

namespace App\Services\Company;

use App\Models\Role;

class RoleService
{

    public function roleList()
    {
        return Role::whereNotIn('name', ['super-admin', 'customer'])->get();
    }
}
