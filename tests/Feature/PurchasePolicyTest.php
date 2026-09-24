<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_but_not_void(): void
    {
        $staff = $this->userWithRole('staff');
        $purchase = new Purchase;

        $this->assertTrue($staff->can('create', Purchase::class));
        $this->assertTrue($staff->can('create', Payment::class));
        $this->assertFalse($staff->can('void', $purchase));
        $this->assertFalse($staff->can('void', new Payment));
        $this->assertFalse($staff->can('update', $purchase));
        $this->assertFalse($staff->can('delete', $purchase));
    }

    public function test_owner_can_void_but_never_edit_or_delete(): void
    {
        $owner = $this->userWithRole('owner');

        $this->assertTrue($owner->can('void', new Purchase));
        $this->assertFalse($owner->can('update', new Purchase));
        $this->assertFalse($owner->can('delete', new Purchase));
    }

    public function test_inactive_user_fails_every_policy(): void
    {
        $owner = $this->userWithRole('owner', ['is_active' => false]);

        $this->assertFalse($owner->can('view', new Purchase));
    }
}
