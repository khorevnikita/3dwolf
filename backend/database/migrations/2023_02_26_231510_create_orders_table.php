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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->float('amount')->default(0);
            $table->dateTime('deadline');
            $table->string('status')->default('new');
            $table->string('payment_status')->default('not_paid');
            $table->text("delivery_address")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
