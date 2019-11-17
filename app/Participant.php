<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'last_name',
        'first_name',
        'mi',
        'sex',
        'station',
        'mobile',
        'email'
    ];
}
