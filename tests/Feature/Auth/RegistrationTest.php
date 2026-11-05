<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'M1guel!Secreto2026',
            'password_confirmation' => 'M1guel!Secreto2026',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_does_not_send_verification_when_requirement_is_disabled(): void
    {
        config(['auth.email_verification_required' => false]);
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'M1guel!Secreto2026',
            'password_confirmation' => 'M1guel!Secreto2026',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertTrue(User::where('email', 'test@example.com')->firstOrFail()->hasVerifiedEmail());
        Notification::assertNothingSent();
    }

    public function test_user_cannot_register_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
