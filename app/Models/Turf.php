<?php

namespace App\Models;

use App\Traits\CreatorUpdator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Turf extends Model
{
    use HasFactory;
    use HasUuids;
    use CreatorUpdator;
    use SoftDeletes;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    /**
     * Get the creator of the turf.
     *
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the updator of the turf.
     *
     */
    public function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
