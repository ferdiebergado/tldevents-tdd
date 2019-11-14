<?php

namespace App\Utils\Model;

use App\User;

trait UserStampTrait
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = auth()->id();
            $model->created_by = $user;
            $model->updated_by = $user;
        });

        static::updating(function ($model) {
            $user = auth()->id();
            $model->updated_by = $user;
        });

        static::deleting(function ($model) {
            $user = auth()->id();
            $model->deleted_by = $user;
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function destroyer()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
