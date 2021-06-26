<?php

namespace Tests\Unit\App\Http\Requests;

use App\Http\Requests\SendBatchRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SendBatchRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Http\Requests\SendBatchRequest */
    private $rules;

    /** @var \Illuminate\Validation\Validator */
    private $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->rules = (new SendBatchRequest)->rules();
        $this->validator = app()->get('validator');
    }

    public function validationProvider()
    {
        /* Poderia usar faker para gerar os casos */
        /* $faker = Factory::create(Factory::DEFAULT_LOCALE); */

        return [
            'sem_anexo' => [
                'passed' => false,
                'data' => [
                    #"file" => '',
                ]
            ],
            'envio_de_xml' => [
                'passed' => false,
                'data' => [
                    "file" => UploadedFile::fake()->create('data.xml', 480)
                ]
            ],
            'envio_de_csv' => [
                'passed' => true,
                'data' => [
                    "file" => UploadedFile::fake()->create('data.csv', 12175480),
                ]
            ],
            'envio_de_txt' => [
                'passed' => true,
                'data' => [
                    "file" => UploadedFile::fake()->create('data.txt', 12175480),
                ]
            ],
            'envio_de_json' => [
                'passed' => false,
                'data' => [
                    "file" => UploadedFile::fake()->create('data.json', 74),
                ]
            ],
            'envio_de_campo_invalido' => [
                'passed' => false,
                'data' => [
                    "arquivo" => UploadedFile::fake()->create('data.txt', 12175480),
                ]
            ],
            'envio_de_image' => [
                'passed' => false,
                'data' => [
                    "file" => UploadedFile::fake()->image('gatinha.png'),
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