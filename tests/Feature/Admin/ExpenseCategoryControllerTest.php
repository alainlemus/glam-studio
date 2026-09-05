<?php

namespace Tests\Feature\Admin;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class ExpenseCategoryControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.expense-categories.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.expense-categories.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        ExpenseCategory::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.expense-categories.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.expense-categories.store'), [
            'name' => 'Renta',
            'type' => 'fixed',
            'description' => 'Renta mensual',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expense_categories', [
            'name' => 'Renta',
            'type' => 'fixed',
        ]);
    }

    public function test_store_requires_valid_type(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.expense-categories.store'), [
            'name' => 'Renta',
            'type' => 'invalid-type',
        ]);

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseCount('expense_categories', 0);
    }

    public function test_admin_can_update_category(): void
    {
        $category = ExpenseCategory::factory()->create(['name' => 'Original', 'type' => 'variable']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.expense-categories.update', $category), [
            'name' => 'Actualizada',
            'type' => 'fixed',
            'description' => 'Nueva descripción',
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'name' => 'Actualizada',
            'type' => 'fixed',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $category = ExpenseCategory::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.expense-categories.destroy', $category));

        $response->assertRedirect();
        $this->assertDatabaseMissing('expense_categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_expenses(): void
    {
        $category = ExpenseCategory::factory()->create();
        Expense::factory()->create(['expense_category_id' => $category->id]);
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.expense-categories.destroy', $category));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('expense_categories', ['id' => $category->id]);
    }
}
