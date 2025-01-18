<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_code');
            $table->string('customer');
            $table->string('address');
            $table->string('phone');
            $table->foreignUuid('delivery_id')->references('id')->on('deliveries')->onUpdate('cascade')->onDelete('cascade');
            $table->float('delivery_fee')->nullable();
            $table->enum('status', ['process', 'on_delivery', 'complete', 'cancled'])->default('process');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};