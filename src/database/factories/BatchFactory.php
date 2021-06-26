<?php

namespace Database\Factories;

use App\Enums\BatchStatusEnum;
use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class BatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Batch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Uuid::uuid4(),
            'status' => BatchStatusEnum::IN_FILE,
            'path' => 'public/files/' .$this->faker->word() . '.csv',
            #'filename' => '',
            #'errors' => null,
            #'ready_at' => null,
        ];
    }
}
