<?php

namespace App\Traits;

trait CreatorUpdator
{

    protected static function bootCreatorUpdator()
    {
        if (auth()->check()) {

            static::creating(function ($model) {
                $model->created_by = auth()->user()->id;
                $model->updated_by = auth()->user()->id;
            });
            static::updating(function ($model) {
                $model->updated_by = auth()->user()->id;
            });
        }
    }
}
