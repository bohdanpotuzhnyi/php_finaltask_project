<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase {
    use RefreshDatabase;

    /**
     * Test that the registration page is accessible.
     *
     * @return void
     */
    public function testRegistrationIsVisible(): void {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test that the registration is possible when values entered correctly.
     *
     * @return void
     */
    public function testRegistrationIsPossible(): void {
        $response = $this->post('/register', [
            'name' => 'Hugo',
            'email' => 'hugo@nase.ch',
            'password' => 'top-secret',
            'password_confirmation' => 'top-secret',
        ]);

        $response->assertRedirect('home');
        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);
    }
}
