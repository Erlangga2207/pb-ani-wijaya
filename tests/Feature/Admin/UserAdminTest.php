<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_open_user_pages(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $other = $this->userWithRole('staff');

        $this->get('/panel/pengguna')->assertForbidden();
        $this->get('/panel/pengguna/create')->assertForbidden();
        $this->get("/panel/pengguna/{$other->id}/edit")->assertForbidden();
        $this->post('/panel/pengguna', $this->userData())->assertForbidden();
    }

    public function test_owner_creates_staff_with_single_role(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->get('/panel/pengguna')->assertOk();
        $this->post('/panel/pengguna', $this->userData())->assertRedirect('/panel/pengguna');

        $user = User::firstWhere('email', 'baru@example.com');
        $this->assertSame(['staff'], $user->getRoleNames()->all());
        $this->assertTrue($user->is_active);
    }

    public function test_owner_cannot_assign_super_admin(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/pengguna', $this->userData(['role' => 'super_admin']))->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);
    }

    public function test_owner_cannot_edit_super_admin(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $super = $this->userWithRole('super_admin');

        $this->get("/panel/pengguna/{$super->id}/edit")->assertForbidden();
        $this->put("/panel/pengguna/{$super->id}", $this->userData(['email' => $super->email]))->assertForbidden();
        $this->delete("/panel/pengguna/{$super->id}")->assertForbidden();
    }

    public function test_password_rules(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/pengguna', $this->userData(['password' => 'pendek', 'password_confirmation' => 'pendek']))
            ->assertSessionHasErrors('password');
        $this->post('/panel/pengguna', $this->userData(['password_confirmation' => 'beda12345']))
            ->assertSessionHasErrors('password');
    }

    public function test_update_keeps_password_when_empty_and_cannot_deactivate_self(): void
    {
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);
        $hash = $owner->password;

        $this->put("/panel/pengguna/{$owner->id}", [
            'name' => 'Pemilik Baru', 'email' => $owner->email, 'password' => '', 'password_confirmation' => '',
            'role' => 'owner', 'is_active' => '0',
        ])->assertRedirect('/panel/pengguna');

        $owner->refresh();
        $this->assertSame('Pemilik Baru', $owner->name);
        $this->assertSame($hash, $owner->password);
        $this->assertTrue($owner->is_active);
    }

    public function test_user_with_transactions_or_self_cannot_be_deleted(): void
    {
        $this->seed(ItemSeeder::class);
        $owner = $this->userWithRole('owner');
        $staff = $this->userWithRole('staff');
        $this->actingAs($staff);
        $this->buyGabah(); // staff mencatat transaksi

        $this->actingAs($owner);
        $this->delete("/panel/pengguna/{$staff->id}")->assertSessionHas('gagal');
        $this->delete("/panel/pengguna/{$owner->id}")->assertSessionHas('gagal');
        $this->assertModelExists($staff);

        $fresh = $this->userWithRole('staff');
        $this->delete("/panel/pengguna/{$fresh->id}")->assertRedirect('/panel/pengguna');
        $this->assertModelMissing($fresh);
    }

    private function userData(array $override = []): array
    {
        return array_merge([
            'name' => 'Staf Baru',
            'email' => 'baru@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'staff',
            'is_active' => '1',
        ], $override);
    }
}
