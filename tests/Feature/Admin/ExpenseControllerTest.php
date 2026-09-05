<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.expenses.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.expenses.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Expense::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.expenses.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_expense(): void
    {
        $category = ExpenseCategory::factory()->create();
        $branch = Branch::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.expenses.store'), [
            'expense_category_id' => $category->id,
            'branch_id' => $branch->id,
            'description' => 'Compra de insumos',
            'amount' => 500,
            'expense_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', ['description' => 'Compra de insumos', 'amount' => 500]);
    }

    public function test_manager_expense_is_forced_to_their_branch(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $category = ExpenseCategory::factory()->create();
        $this->actingAs($this->manager($ownBranch));

        $this->post(route('admin.expenses.store'), [
            'expense_category_id' => $category->id,
            'branch_id' => $otherBranch->id,
            'description' => 'Gasto',
            'amount' => 100,
            'expense_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('expenses', ['description' => 'Gasto', 'branch_id' => $ownBranch->id]);
    }

    public function test_admin_can_export_csv(): void
    {
        Expense::factory()->create(['description' => 'Compra de prueba']);
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.expenses.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Compra de prueba', $response->streamedContent());
    }

    public function test_export_respects_branch_scope(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        Expense::factory()->create(['branch_id' => $ownBranch->id, 'description' => 'Gasto propio']);
        Expense::factory()->create(['branch_id' => $otherBranch->id, 'description' => 'Gasto ajeno']);
        $this->actingAs($this->manager($ownBranch));

        $content = $this->get(route('admin.expenses.export'))->streamedContent();

        $this->assertStringContainsString('Gasto propio', $content);
        $this->assertStringNotContainsString('Gasto ajeno', $content);
    }

    public function test_admin_can_update_expense(): void
    {
        $expense = Expense::factory()->create(['amount' => 100]);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.expenses.update', $expense), [
            'expense_category_id' => $expense->expense_category_id,
            'branch_id' => $expense->branch_id,
            'description' => 'Actualizado',
            'amount' => 250,
            'expense_date' => now()->format('Y-m-d'),
            'payment_method' => 'card',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'amount' => 250, 'description' => 'Actualizado']);
    }

    public function test_admin_can_delete_expense(): void
    {
        $expense = Expense::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.expenses.destroy', $expense));

        $response->assertRedirect();
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
