<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name', 255);
            $table->string('sku', 255)->unique();
            $table->text('description');
            $table->decimal('base_price', 10, 2);
            $table->tinyInteger('is_active')->comment("0=>inactive,1=>active")->default('1');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Index for category filtering
            $table->index('category_id');

            // Index for price filtering
            $table->index('base_price');

            // Fulltext index for search by name and SKU
            $table->fullText(['name', 'sku']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('products');
    }
};
