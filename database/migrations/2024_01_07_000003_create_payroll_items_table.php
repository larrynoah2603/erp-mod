<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['salary', 'overtime', 'bonus', 'deduction', 'charge']);
            $table->string('label');
            $table->decimal('amount', 10, 2);
            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'payroll_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_items');
    }
};