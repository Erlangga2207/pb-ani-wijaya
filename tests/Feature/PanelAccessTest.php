<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-08 Fase 7: dulu /admin (Filament). User nonaktif, tanpa role, dan tamu diuji di Admin\AuthTest.
class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_with_role_can_access_panel(): void
    {
        foreach (User::ROLES as $role) {
            $this->actingAs($this->userWithRole($role))->get('/panel')->assertOk();
        }
    }
}
