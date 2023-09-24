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
        Schema::create('payment_purposes', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("color");
            $table->timestamps();
        });

        Schema::table("payments", function (Blueprint $table) {
            $table->foreignId("payment_purpose_id")->nullable()->constrained()->nullOnDelete();
        });

        Schema::table("user_permissions", function (Blueprint $table) {
            $table->boolean("payment_purpose")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_permissions", function (Blueprint $table) {
            $table->dropColumn("payment_purpose");
        });

        Schema::table("payments", function (Blueprint $table) {
            $table->dropConstrainedForeignId("payment_purpose_id");
        });

        Schema::dropIfExists('payment_purposes');
    }
};
