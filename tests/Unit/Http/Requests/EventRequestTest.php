<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\EventRequest;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class EventRequestTest extends TestCase
{
    /** @var \App\Http\Requests\EventRequest */
    private $rules;

    /** @var \Illuminate\Validation\Validator */
    private $validator;

    /** @inheritDoc */
    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = app()->get('validator');

        $this->rules = (new EventRequest())->rules();
    }

    /**
     * Data Provider
     *
     * @return array|Iterable
     */
    public function validationProvider()
    {
        $faker = Factory::create();

        $data = [
            'title' => $faker->text,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($faker->numberBetween(1, 30))->toDateString(),
            'type' => $faker->randomElement(['W', 'T', 'C']),
            'grouping' => $faker->randomElement(['R', 'L', 'M', 'N'])
        ];

        return [
            'request_should_fail_when_no_title_is_provided' => [
                'passed' => false,
                'data' => Arr::except($data, 'title')
            ],
            'request_should_fail_when_no_start_date_is_provided' => [
                'passed' => false,
                'data' => Arr::except($data, 'start_date')
            ],
            'request_should_fail_when_no_end_date_is_provided' => [
                'passed' => false,
                'data' => Arr::except($data, 'end_date')
            ],
            'request_should_fail_when_end_date_is_earlier_than_started_at' => [
                'passed' => false,
                'data' => array_merge(Arr::except($data, 'end_date'), ['end_date' => now()->subDay()->toDateString()])
            ],
            'request_should_fail_when_no_type_is_provided' => [
                'passed' => false,
                'data' => Arr::except($data, 'type')
            ],
            'request_should_fail_when_type_is_not_in_the_list' => [
                'passed' => false,
                'data' => array_merge(Arr::except($data, 'type'), ['type' => 'X'])
            ],
            'request_should_fail_when_no_grouping_is_provided' => [
                'passed' => false,
                'data' => Arr::except($data, 'grouping')
            ],
            'request_should_fail_when_grouping_is_not_in_the_list' => [
                'passed' => false,
                'data' => array_merge(Arr::except($data, 'grouping'), ['type' => 'X'])
            ],
            'request_should_pass_when_data_is_provided' => [
                'passed' => true,
                'data' => $data
            ]
        ];
    }

    /**
     * @dataProvider validationProvider
     *
     * @param bool $shouldPass
     * @param array $mockedRequestData
     * @return void
     */
    public function testValidationResultsAsExpected(bool $shouldPass, array $mockedRequestData)
    {
        $this->assertEquals($shouldPass, $this->validate($mockedRequestData));
    }

    /**
     * Validate the request
     *
     * @param array $mockedRequestData
     * @return bool
     */
    protected function validate($mockedRequestData)
    {
        return $this->validator->make($mockedRequestData, $this->rules)->passes();
    }
}
