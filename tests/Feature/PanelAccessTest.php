<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_with_role_can_access_panel(): void
    {
        foreach (User::ROLES as $role) {
            $this->actingAs($this->userWithRole($role))->get('/admin')->assertOk();
        }
    }

    public function test_inactive_user_is_forbidden(): void
    {
        $user = $this->userWithRole('owner', ['is_active' => false]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_user_without_role_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}
