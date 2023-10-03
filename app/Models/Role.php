<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role as BaseRoleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends BaseRoleModel
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];
}
