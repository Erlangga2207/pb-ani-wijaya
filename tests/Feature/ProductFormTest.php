<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFormTest extends TestCase
{
    use RefreshDatabase;

    // PRD-08 Fase 6: dulu lewat form Filament (Livewire), kini lewat /panel. Assertion slug tetap sama.
    public function test_slug_is_generated_from_name(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/katalog', [
            'name' => ['id' => 'Beras Ketan Putih Premium'],
            'weight_per_karung' => 25,
            'is_active' => '1',
        ])->assertRedirect('/panel/katalog')->assertSessionHasNoErrors();

        $this->assertSame('beras-ketan-putih-premium', Product::first()->slug);
    }
}
