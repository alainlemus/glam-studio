<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (ExpenseCategory::defaults() as $cat) {
            $cat['slug'] = \Illuminate\Support\Str::slug($cat['name']);
            ExpenseCategory::firstOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}