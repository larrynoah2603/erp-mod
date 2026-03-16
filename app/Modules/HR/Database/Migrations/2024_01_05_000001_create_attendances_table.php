<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->datetime('check_in');
            $table->datetime('check_out')->nullable();
            $table->enum('status', ['present', 'late', 'absent'])->default('present');
            $table->string('ip_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('working_hours', 5, 2)->nullable(); // heures décimales
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'user_id', 'check_in']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};