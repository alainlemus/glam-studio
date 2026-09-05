<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Income;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class IncomeControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.incomes.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.incomes.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Income::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.incomes.index'));
        $response->assertOk();
    }

    public function test_admin_can_export_csv(): void
    {
        Income::factory()->create(['concept' => 'Ingreso de prueba']);
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.incomes.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Ingreso de prueba', $response->streamedContent());
    }

    public function test_export_respects_branch_scope(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        Income::factory()->create(['branch_id' => $ownBranch->id, 'concept' => 'Ingreso propio']);
        Income::factory()->create(['branch_id' => $otherBranch->id, 'concept' => 'Ingreso ajeno']);
        $this->actingAs($this->manager($ownBranch));

        $content = $this->get(route('admin.incomes.export'))->streamedContent();

        $this->assertStringContainsString('Ingreso propio', $content);
        $this->assertStringNotContainsString('Ingreso ajeno', $content);
    }

    public function test_admin_can_delete_income(): void
    {
        $income = Income::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.incomes.destroy', $income));

        $response->assertRedirect();
        $this->assertDatabaseMissing('incomes', ['id' => $income->id]);
    }

    public function test_manager_cannot_delete_income_from_other_branch(): void
    {
        $branch = Branch::factory()->create();
        $income = Income::factory()->create();
        $this->actingAs($this->manager($branch));

        $response = $this->delete(route('admin.incomes.destroy', $income));

        $response->assertForbidden();
    }
}
