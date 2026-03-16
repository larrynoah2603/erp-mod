<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // créé par
            $table->string('invoice_number')->unique();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_address')->nullable();
            $table->string('client_vat')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'invoice_number']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'due_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};