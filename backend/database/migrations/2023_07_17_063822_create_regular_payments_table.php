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
        Schema::create('regular_payments', function (Blueprint $table) {
            $table->id();
            $table->string("schedule");
            $table->string("recipient");
            $table->foreignId("user_id")->nullable()->constrained()->cascadeOnDelete();
            $table->float("amount");
            $table->text("description")->nullable();
            $table->timestamps();
        });

        Schema::table("user_permissions", function (Blueprint $table) {
            $table->boolean("regular_payments")->after("payments")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_permissions", function (Blueprint $table) {
            $table->dropColumn("regular_payments");
        });
        Schema::dropIfExists('regular_payments');
    }
};
