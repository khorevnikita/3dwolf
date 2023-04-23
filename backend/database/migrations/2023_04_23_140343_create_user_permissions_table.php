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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->boolean('users')->default(0);
            $table->boolean('customers')->default(0);
            $table->boolean('materials')->default(0);
            $table->boolean('manufacturers')->default(0);
            $table->boolean('parts')->default(0);
            $table->boolean('accounts')->default(0);
            $table->boolean('orders')->default(0);
            $table->boolean('contracts')->default(0);
            $table->boolean('payments')->default(0);
            $table->boolean('estimates')->default(0);
            $table->boolean('newsletters')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
