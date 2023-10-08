<?php

namespace App\Models;

use App\Traits\CreatorUpdator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use CreatorUpdator;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];


    public function turf()
    {
        return $this->belongsTo(Turf::class);
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
