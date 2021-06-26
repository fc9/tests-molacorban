<?php

namespace Tests\Unit\App\Http\Requests;

use App\Http\Requests\ShowBatchRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ShowBatchRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Http\Requests\ShowBatchRequest */
    private $rules;

    /** @var \Illuminate\Validation\Validator */
    private $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->rules = (new ShowBatchRequest)->rules();
        $this->validator = app()->get('validator');
    }

    public function validationProvider()
    {
        return [
            'sem_uuid' => [
                'passed' => false,
                'data' => [
                    #"uuid" => '',
                ]
            ],
            'uuid_valido' => [
                'passed' => true,
                'data' => [
                    "uuid" => Uuid::uuid4()
                ]
            ],
            'uuid_invalido' => [
                'passed' => false,
                'data' => [
                    "uuid" => '111b17010000-45d3-a418-5a0165ecfedf'
                ]
            ],
            'uuid_numerico' => [
                'passed' => false,
                'data' => [
                    "uuid" => 123
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validationProvider
     * @param bool $shouldPass
     * @param array $mockedRequestData
     */
    public function validation_results_as_expected(bool $shouldPass, array $mockedRequestData)
    {


        $this->assertEquals(
            $shouldPass,
            $this->validate($mockedRequestData)
        );
    }

    protected function validate($mockedRequestData)
    {
        return $this->validator
            ->make($mockedRequestData, $this->rules)
            ->passes();
    }
}