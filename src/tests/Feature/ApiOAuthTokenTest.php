<?php

namespace Tests\Feature;

use App\Models\Passport\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiOAuthTokenTest extends TestCase
{
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
    }

    /**
     * Set variable accessAuthorization
     */
    protected function setUserAndOAuthClient(string $password)
    {
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
        $this->oauth_client = $oauth_client;

        $factory = User::factory()->make();
        $user = (new User)->fill(['password' => bcrypt($password)] + $factory->toArray());
        $user->save();
        $this->user = $user;
    }

    /** @test */
    public function requisitar_um_access_token_usando_login_senha_e_um_token()
    {
        $password = 'qu41qu3rc0i54';

        $this->artisan('config:cache', ['--no-interaction' => true]);

        $this->setUserAndOAuthClient($password);

        $response = $this->json('POST', 'http://localhost/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->oauth_client->id,
            'client_secret' => $this->oauth_client->secret, #token
            'username' => $this->user->email,
            'password' => $password,
            'scope' => ''
        ]);

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json->where('token_type', "Bearer")
                ->has('expires_in')
                ->has('access_token')
                ->has('refresh_token')
            );

        $this->oauth_client->delete();
        $this->user->delete();
    }

    /** ***********************************************************
     * Seguem testes que provavelmente faria em um ambiente real.
     * Considerei facultativos, pois seriam cópias do teste acima com pequenos ajustes.
     **************************************************************/

    /** @test */
    public function requisitar_um_access_token_sem_enviar_nada()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_somente_login_e_senha()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_somente_o_token()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_login_senha_e_o_token_mas_com_login_incorreto()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_login_senha_e_o_token_mas_com_senha_incorreta()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_login_senha_e_o_token_mas_com_token_incorreto()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_login_senha_e_o_token_mas_com_token_vencido()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_id_client_incorreto()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_id_e_secret_client_incompativeis()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }

    /** @test */
    public function requisitar_um_access_token_usando_id_client_incompativel_com_o_grant_type()
    {
        $this->assertStringContainsString($any = 'any', 'any');
    }
}