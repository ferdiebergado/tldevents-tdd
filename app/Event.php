<?php

namespace App;

use App\BaseModel;
use App\Utils\Model\UserStampTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends BaseModel
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
        'type_keys',
        'type_names',
        'grouping_name',
        'grouping_keys',
        'grouping_names'
    ];

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->attributes['type']];
    }

    public function getGroupingNameAttribute()
    {
        return self::GROUPINGS[$this->attributes['grouping']];
    }

    public function getTypeKeysAttribute()
    {
        return array_keys(self::TYPES);
    }

    public function getTypeNamesAttribute()
    {
        return array_values(self::TYPES);
    }

    public function getGroupingKeysAttribute()
    {
        return array_keys(self::GROUPINGS);
    }

    public function getGroupingNamesAttribute()
    {
        return array_values(self::GROUPINGS);
    }
}
