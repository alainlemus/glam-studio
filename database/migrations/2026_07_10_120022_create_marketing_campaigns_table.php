<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['whatsapp', 'sms', 'email', 'promotion'])->default('whatsapp');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->text('message_template')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'active', 'finished', 'cancelled'])->default('draft');
            $table->integer('target_audience')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};