<?php

namespace Tests\Feature;

use App\Models\Production;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_and_view_but_not_void(): void
    {
        $staff = $this->userWithRole('staff');

        $this->assertTrue($staff->can('create', Production::class));
        $this->assertTrue($staff->can('view', new Production));
        $this->assertFalse($staff->can('void', new Production));
        $this->assertFalse($staff->can('update', new Production));
    }

    public function test_owner_can_void(): void
    {
        $this->assertTrue($this->userWithRole('owner')->can('void', new Production));
    }
}
