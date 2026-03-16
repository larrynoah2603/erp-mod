<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('employee_id')->unique()->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->enum('contract_type', ['CDI', 'CDD', 'INTERIM', 'FREELANCE'])->nullable();
            $table->json('contract_settings')->nullable();
            $table->rememberToken();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'email']);
            $table->index(['company_id', 'department']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};