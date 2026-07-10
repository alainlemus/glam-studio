<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->string('concept');
            $table->decimal('amount', 10, 2);
            $table->date('income_date');
            $table->enum('source', ['sale', 'service', 'other'])->default('service');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['income_date', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};