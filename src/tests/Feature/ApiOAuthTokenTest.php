<?php

namespace Tests\Feature;

use App\Models\Passport\Client;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiOAuthTokenTest extends TestCase
{
    /** @test */
    public function requisitar_um_access_token_usando_login_senha_e_um_token()
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
        $user = (new User)->fill(['name' => 'test', 'email' => 'test@example.com', 'password' => bcrypt($password)]);
        $user->save();

        $response = $this->json('POST', 'http://localhost/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $oauth_client->id,
            'client_secret' => $oauth_client->secret, #token
            'username' => $user->email,
            'password' => $password,
            'scope' => ''
        ]);

        $oauth_client->delete();
        $user->delete();

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('token_type', "Bearer")
                    ->has('expires_in')
                    ->has('access_token')
                    ->has('refresh_token')
            );
    }
}