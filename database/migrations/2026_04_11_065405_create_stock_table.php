<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->timestamps();

            // Prevent duplicate stock rows
            $table->unique(['product_id', 'warehouse_id']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            // Composite index for stock lookup
            $table->index(['product_id', 'warehouse_id']);

        });

        // Ensure valid stock values
        // Laravel doesn’t support check constraints directly, so I used raw SQL to enforce them at DB level. Additionally, I validate in the service layer to ensure safety.
        DB::statement('ALTER TABLE stock ADD CONSTRAINT chk_quantity CHECK (quantity >= 0)');
        DB::statement('ALTER TABLE stock ADD CONSTRAINT chk_reserved CHECK (reserved_quantity >= 0)');
        DB::statement('ALTER TABLE stock ADD CONSTRAINT chk_reserved_less CHECK (reserved_quantity <= quantity)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['warehouse_id']);
        });
        Schema::dropIfExists('stock');
    }
};
