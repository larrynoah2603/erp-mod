<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
            
            $table->index(['company_id', 'invoice_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};