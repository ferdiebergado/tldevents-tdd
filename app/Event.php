<?php

namespace App;

use App\Utils\Model\UserStampTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use UserStampTrait;
    use SoftDeletes;

    private const TYPES = [
        'W' => 'Workshop/Writeshop',
        'T' => 'Training/Orientation',
        'C' => 'Conference/Summit'
    ];

    private const GROUPINGS = [
        'R' => 'By Region',
        'L' => 'By Learning Area',
        'M' => 'By Language',
        'N' => 'No Grouping'
    ];

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'type',
        'grouping',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    protected $appends = [
        'type_name',
        'grouping_name'
    ];

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->attributes['type']];
    }

    public function getGroupingNameAttribute()
    {
        return self::GROUPINGS[$this->attributes['grouping']];
    }
}
