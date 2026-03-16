<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(5);
            $table->integer('max_quantity')->nullable();
            $table->string('unit')->default('piece');
            $table->string('category')->nullable();
            $table->string('supplier')->nullable();
            $table->string('location')->nullable(); // emplacement en stock
            $table->json('attributes')->nullable(); // attributs supplémentaires
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'sku']);
            $table->index(['company_id', 'category']);
            $table->index('quantity');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};