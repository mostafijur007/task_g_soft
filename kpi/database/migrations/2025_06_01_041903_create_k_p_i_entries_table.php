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
        Schema::create('k_p_i_entries', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Format: KPI-0001');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('month')->comment('Stores year-month (YYYY-MM)');
            $table->string('uom');
            $table->integer('quantity');
            $table->decimal('asp', 10, 2)->comment('Average Selling Price');
            $table->decimal('total_value', 12, 2)->comment('Calculated: quantity * asp');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('k_p_i_entries');
    }
};
