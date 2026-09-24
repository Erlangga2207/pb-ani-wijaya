<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Expenses\Pages\CreateExpense;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_records_expense_via_form(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        Livewire::test(CreateExpense::class)
            ->fillForm([
                'expense_date' => today()->toDateString(),
                'category' => 'fuel',
                'description' => 'Solar genset',
                'amount' => 250000,
                'method' => 'cash',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $expense = Expense::first();
        $this->assertMatchesRegularExpression('/^BO-\d{8}-0001$/', $expense->number);
        $this->assertEquals(250000, (float) $expense->amount);

        $this->get('/admin/expenses')->assertOk()->assertSee('Solar genset');
        $this->get("/admin/expenses/{$expense->id}")->assertOk();
    }

    public function test_staff_has_no_access(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/admin/expenses')->assertForbidden();
        $this->get('/admin/expenses/create')->assertForbidden();
    }
}
