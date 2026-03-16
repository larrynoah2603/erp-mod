<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('base_salary', 10, 2);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->enum('pay_type', ['monthly', 'hourly', 'daily'])->default('monthly');
            $table->decimal('overtime_rate', 5, 2)->default(1.25);
            $table->decimal('night_rate', 5, 2)->default(1.5);
            $table->decimal('holiday_rate', 5, 2)->default(2.0);
            $table->json('bonuses')->nullable();
            $table->json('deductions')->nullable();
            $table->json('social_charges')->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['company_id', 'user_id', 'effective_from']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_configs');
    }
};