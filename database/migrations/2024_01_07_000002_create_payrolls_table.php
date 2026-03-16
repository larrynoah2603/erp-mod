<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payroll_number')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('payment_date');
            $table->decimal('base_salary', 10, 2);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->decimal('overtime_amount', 10, 2)->default(0);
            $table->decimal('bonuses', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('social_charges', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->decimal('gross_salary', 10, 2);
            $table->json('details')->nullable();
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'user_id', 'period_start', 'period_end']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};