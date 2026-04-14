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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->tinyInteger('movement_type')->comment('1=>stock_in, 2=>stock_out, 3=>reservation, 4=>reservation_release');
            $table->integer('quantity')->default(0);
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_type');
            $table->text('note');
            $table->dateTime('moved_at');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            // Index for filtering + reporting
            $table->index(['product_id', 'warehouse_id']);
            $table->index('movement_type');
            $table->index('moved_at');

            // Optimized for reporting queries
            $table->index(['product_id', 'moved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['warehouse_id']);
        });
        Schema::dropIfExists('stock_movements');
    }
};
