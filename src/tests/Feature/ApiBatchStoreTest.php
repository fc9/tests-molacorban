<?php

namespace Tests\Feature;

use App\Enums\BatchStatusEnum;
use App\Models\Passport\Client;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiBatchStoreTest extends TestCase
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

    /** @test */
    public function enviar_batch_com_arquivo()
    {
        $response = $this->json('POST',
            "http://localhost/api/v1/batches/",
            ['file' => UploadedFile::fake()->create('test.csv', 480)],
            [
                'Authorization' => $this->accessAuthorization['token_type'] . ' ' . $this->accessAuthorization['access_token'],
                'Content-Type' => 'multipart/form-data'
            ]
        );

        $response->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('success', true)
                ->has('uuid')
                ->has('links')
                ->etc()
            );
    }

    /** @test */
    public function enviar_batch_sem_arquivo()
    {
        $response = $this->json('POST',
            "http://localhost/api/v1/batches/",
            ['file' => null],
            [
                'Authorization' => $this->accessAuthorization['token_type'] . ' ' . $this->accessAuthorization['access_token'],
                'Content-Type' => 'multipart/form-data'
            ]
        );

        $response->assertStatus(422)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('errors')
            );
    }

    /** @test */
    public function enviar_batch_com_arquivo_de_outro_tipo()
    {
        $response = $this->json('POST',
            "http://localhost/api/v1/batches/",
            ['file' => UploadedFile::fake()->image('travel.jpg', 480)],
            [
                'Authorization' => $this->accessAuthorization['token_type'] . ' ' . $this->accessAuthorization['access_token'],
                'Content-Type' => 'multipart/form-data'
            ]
        );

        $response->assertStatus(422)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('errors')
            );
    }

}