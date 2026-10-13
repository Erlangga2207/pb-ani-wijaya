<?php

namespace Tests\Feature;

use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-08 Fase 7: dulu lewat form & halaman Filament, kini /panel/biaya. Data & assertion sama.
class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_records_expense_via_form(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/biaya', [
            'expense_date' => today()->toDateString(),
            'category' => 'fuel',
            'description' => 'Solar genset',
            'amount' => 250000,
            'method' => 'cash',
        ])->assertSessionHasNoErrors();

        $expense = Expense::first();
        $this->assertMatchesRegularExpression('/^BO-\d{8}-0001$/', $expense->number);
        $this->assertEquals(250000, (float) $expense->amount);

        $this->get('/panel/biaya')->assertOk()->assertSee('Solar genset');
        $this->get("/panel/biaya/{$expense->id}")->assertOk();
    }

    public function test_staff_has_no_access(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/panel/biaya')->assertForbidden();
        $this->get('/panel/biaya/create')->assertForbidden();
    }
}
