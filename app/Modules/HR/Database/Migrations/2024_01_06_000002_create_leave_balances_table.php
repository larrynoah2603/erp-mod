<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('annual_total')->default(25); // Congés annuels totaux
            $table->integer('annual_used')->default(0);
            $table->integer('annual_remaining')->virtualAs('annual_total - annual_used', true);
            $table->integer('sick_total')->default(0);
            $table->integer('sick_used')->default(0);
            $table->integer('personal_total')->default(0);
            $table->integer('personal_used')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['company_id', 'user_id', 'year']);
            $table->index(['company_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_balances');
    }
};