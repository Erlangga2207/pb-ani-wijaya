<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Products\Pages\CreateProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_from_name(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        Livewire::test(CreateProduct::class)
            ->fillForm(['name' => 'Beras Ketan Putih Premium', 'weight_per_karung' => 25])
            ->assertSchemaStateSet(['slug' => 'beras-ketan-putih-premium'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('beras-ketan-putih-premium', Product::first()->slug);
    }
}
