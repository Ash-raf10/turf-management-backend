<?php

namespace App\Models;

use App\Traits\CreatorUpdator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slot extends Model
{
    use HasFactory;
    use HasUuids;
    use CreatorUpdator;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
