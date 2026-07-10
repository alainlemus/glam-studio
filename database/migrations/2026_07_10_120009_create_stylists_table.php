<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stylists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('specialty')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->decimal('service_commission', 5, 2)->default(25.00);
            $table->decimal('product_commission', 5, 2)->default(10.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stylists');
    }
};