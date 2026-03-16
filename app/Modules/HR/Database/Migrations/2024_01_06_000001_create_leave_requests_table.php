<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->enum('type', ['annual', 'sick', 'personal', 'maternity', 'other']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'user_id', 'status']);
            $table->index(['company_id', 'start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_requests');
    }
};