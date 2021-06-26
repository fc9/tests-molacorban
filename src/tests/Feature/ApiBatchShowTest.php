<?php

namespace Tests\Feature;

use App\Enums\BatchStatusEnum;
use App\Models\Batch;
use App\Models\Passport\Client;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ApiBatchShowTest extends TestCase
{
    /** @var object */
    protected $accessAuthorization;

    /** @var Client */
    protected $oauth_client;

    /** @var User */
    protected $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setAccessAuthorization();
    }

    /**
     * Set variable accessAuthorization
     */
    protected function setAccessAuthorization()
    {
        $this->artisan('config:cache', ['--no-interaction' => true]);

        $oauth_client = (new Client)->fill([
            'user_id' => null,
            'name' => 'test',
            'secret' => 'RLWQwhvTBE0JeUrkDWp1hkVP5YxrD0cJkBaRSJwG',
            'provider' => 'users',
            'redirect' => env('APP_URL'),
            'password_client' => true,
            'personal_access_client' => false,
            'revoked' => false
        ]);
        $oauth_client->save();

        $password = 'qu41qu3rc0i54';
        $factory = User::factory()->make();
        $user = (new User)->fill(['password' => bcrypt($password)] + $factory->toArray());
        $user->save();

        $response = $this->json('POST', 'http://localhost/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $oauth_client->id,
            'client_secret' => $oauth_client->secret, #token
            'username' => $user->email,
            'password' => $password,
            'scope' => ''
        ]);

        $this->oauth_client = $oauth_client;
        $this->user = $user;
        $this->accessAuthorization = $response->json();
    }

    /**
     * Create a Batch register
     */
    public function createBatch(BatchStatusEnum $status)
    {
        $batch = (new Batch)->fill(Batch::factory()->make()->toArray());
        $batch->uuid = Uuid::uuid4();
        $batch->status = $status->value;
        $batch->save();

        return $batch;
    }

    /** @test */
    public function acessar_batch_sem_enviar_uuid()
    {
        $response = $this->json('GET',
            "http://localhost/api/v1/batches",
            [],
            [
                'Authorization' => $this->accessAuthorization['token_type'] . ' ' . $this->accessAuthorization['access_token'],
            ]
        );

        $response->assertStatus(404)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('errors')
            );
    }

    /** @test */
    public function acessar_batch_com_status_em_arquivo()
    {
        $batch = $this->createBatch(BatchStatusEnum::fromKey('IN_FILE'));
        $uuid = (string) $batch->uuid;

        $response = $this->json('GET',
            "http://localhost/api/v1/batches/{$uuid}",
            ['uuid' => $uuid],
            ['Authorization' => $this->accessAuthorization['token_type'] . ' ' . $this->accessAuthorization['access_token'],]
        );

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('uuid', $uuid)
                ->where('status', BatchStatusEnum::fromKey('LOADING')->description)
                ->etc()
            );
    }

    /** @test */
    public function acessar_batch_com_status_de_error()
    {
        $batch = $this->createBatch(BatchStatusEnum::fromKey('ERROR'));
        $uuid = (string) $batch->uuid;

        $response = $this->json('GET',
            "http://localhost/api/v1/batches/{$uuid}",
            ['uuid' => $uuid],
            ['Authorization' => $this->accessAuthorization['token_type'] . ' ' . $this->accessAuthorization['access_token'],]
        );

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('uuid', $uuid)
                ->where('status', $batch->status->description)
                ->etc()
            );
    }

    /** @test */
    public function acessar_batch_com_status_carregando()
    {
        $batch = $this->createBatch(BatchStatusEnum::fromKey('LOADING'));
        $uuid = (string) $batch->uuid;

        $response = $this->json('GET',
            "http://localhost/api/v1/batches/{$uuid}",
            ['uuid' => $uuid],
            ['Authorization' => $this->accessAuthorization['token_type'] . ' ' . $this->accessAuthorization['access_token'],]
        );

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('uuid', $uuid)
                ->where('status', $batch->status->description)
                ->etc()
            );

        $batch->delete();
        $this->oauth_client->delete();
        $this->user->delete();
    }

}