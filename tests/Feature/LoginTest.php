<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase {
    use RefreshDatabase;

    /**
     * Test that the login page is accessible.
     *
     * @return void
     */
    public function testLoginPageIsAccessible(): void {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Test that the login is possible when values entered correctly.
     *
     * @return void
     */
    public function testUserCanLoginViaLoginForm(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Try to login with correct credentials
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('home');
    }

    /**
     * Test that the login is not possible when values entered incorrectly.
     *
     * @return void
     */
    public function testUserWithWrongPasswordCannotGetInside(): void {
        $user = User::factory()->create();

        // Try to login with wrong password
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'my-2nd-attempt',
        ]);

        // Check that user is not authenticated
        $this->assertGuest();
    }
}
