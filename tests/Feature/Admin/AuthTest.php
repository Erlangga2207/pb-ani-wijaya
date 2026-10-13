<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_panel_login(): void
    {
        $this->get('/panel')->assertRedirect('/panel/login');
        $this->get('/panel/login')->assertOk()->assertSee('Masuk ke panel');
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = $this->userWithRole('staff', ['password' => 'rahasia123']);

        $this->post('/panel/login', ['email' => $user->email, 'password' => 'rahasia123'])
            ->assertRedirect('/panel');
        $this->assertAuthenticatedAs($user);

        $this->post('/panel/logout')->assertRedirect('/panel/login');
        $this->assertGuest();
    }

    public function test_logout_requires_post(): void
    {
        $this->actingAs($this->userWithRole('staff'))->get('/panel/logout')->assertMethodNotAllowed();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->userWithRole('owner', ['password' => 'rahasia123']);

        $this->post('/panel/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_validation_errors(): void
    {
        $this->post('/panel/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        foreach (range(1, 5) as $i) {
            $this->post('/panel/login', ['email' => 'x@example.com', 'password' => 'salah']);
        }

        $this->post('/panel/login', ['email' => 'x@example.com', 'password' => 'salah'])->assertTooManyRequests();
    }

    public function test_inactive_user_is_logged_out(): void
    {
        $user = $this->userWithRole('owner', ['is_active' => false]);

        $this->actingAs($user)->get('/panel')
            ->assertRedirect('/panel/login')
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_without_role_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())->get('/panel')->assertForbidden();
    }

    public function test_logged_in_user_visiting_login_goes_to_dashboard(): void
    {
        $this->actingAs($this->userWithRole('staff'))->get('/panel/login')->assertRedirect('/panel');
    }
}
