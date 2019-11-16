<?php

namespace Tests\Unit;

use App\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function testGetTypeNameAttribute()
    {
        foreach ($this->data()['types'] as $key => $value) {
            $event = factory(Event::class)->create(['type' => $key]);
            $event->toArray();
            $this->assertEquals($value, $event['type_name']);
        }
    }

    public function testGetGroupingNameAttribute()
    {
        foreach ($this->data()['groupings'] as $key => $value) {
            $event = factory(Event::class)->create(['grouping' => $key]);
            $event->toArray();
            $this->assertEquals($value, $event['grouping_name']);
        }
    }

    public function testGetTypeKeysAttribute()
    {
        $event = factory(Event::class)->create();

        $this->assertEquals(array_keys($this->data()['types']), $event->typeKeys);
    }

    public function testGetTypeNamesAttribute()
    {
        $event = factory(Event::class)->create();

        $this->assertEquals(array_values($this->data()['types']), $event->typeNames);
    }

    public function testGetGroupingKeysAttribute()
    {
        $event = factory(Event::class)->create();

        $this->assertEquals(array_keys($this->data()['groupings']), $event->groupingKeys);
    }

    public function testGetGroupingNamesAttribute()
    {
        $event = factory(Event::class)->create();

        $this->assertEquals(array_values($this->data()['groupings']), $event->groupingNames);
    }

    protected function data()
    {
        return [
            'types' => [

                'W' => 'Workshop/Writeshop',
                'T' => 'Training/Orientation',
                'C' => 'Conference/Summit'
            ],
            'groupings' => [
                'R' => 'By Region',
                'L' => 'By Learning Area',
                'M' => 'By Language',
                'N' => 'No Grouping'
            ]
        ];
    }
}
