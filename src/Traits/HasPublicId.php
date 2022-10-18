<?php

namespace Tools\Traits;

use Illuminate\Support\Str;

trait HasPublicId
{
    public static function bootHasPublicId()
    {
        self::creating(function ($model) {
            $model->public_id = Str::uuid();
        });
    }
}
