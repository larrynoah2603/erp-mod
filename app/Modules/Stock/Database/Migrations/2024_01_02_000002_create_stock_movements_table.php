<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->enum('type', ['in', 'out', 'adjustment', 'transfer']);
            $table->integer('quantity');
            $table->integer('before_quantity');
            $table->integer('after_quantity');
            $table->text('reason')->nullable();
            $table->string('reference_type')->nullable(); // invoice, order, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};