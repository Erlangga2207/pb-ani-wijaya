<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_cannot_update_or_delete_super_admin(): void
    {
        $owner = $this->userWithRole('owner');
        $super = $this->userWithRole('super_admin');

        $this->assertFalse($owner->can('update', $super));
        $this->assertFalse($owner->can('delete', $super));
        $this->assertTrue($owner->can('update', $this->userWithRole('staff')));
    }

    public function test_owner_cannot_open_super_admin_edit_page(): void
    {
        $owner = $this->userWithRole('owner');
        $super = $this->userWithRole('super_admin');

        $this->actingAs($owner)->get("/admin/users/{$super->id}/edit")->assertForbidden();
    }

    public function test_owner_cannot_assign_super_admin_role(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Penyusup',
                'email' => 'x@example.com',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => 'super_admin',
            ])
            ->call('create')
            ->assertHasFormErrors(['role']);

        $this->assertDatabaseMissing('users', ['email' => 'x@example.com']);
    }

    public function test_owner_can_create_staff_with_single_role(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Staff Baru',
                'email' => 'staff@example.com',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => 'staff',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'staff@example.com')->first();
        $this->assertSame(['staff'], $user->getRoleNames()->all());
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'A',
                'email' => 'a@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
                'role' => 'staff',
            ])
            ->call('create')
            ->assertHasFormErrors(['password']);
    }

    public function test_user_cannot_deactivate_or_delete_self(): void
    {
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);

        $this->assertFalse($owner->can('delete', $owner));

        Livewire::test(EditUser::class, ['record' => $owner->getRouteKey()])
            ->fillForm(['is_active' => false])
            ->call('save');

        $this->assertTrue($owner->fresh()->is_active);
    }

    public function test_staff_cannot_manage_users(): void
    {
        $this->actingAs($this->userWithRole('staff'))->get('/admin/users')->assertForbidden();
    }
}
