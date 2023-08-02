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
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("text")->nullable();
            $table->timestamps();
        });

        Schema::table("user_permissions", function (Blueprint $table) {
            $table->boolean("delivery_address")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_permissions", function (Blueprint $table) {
            $table->dropColumn("delivery_address");
        });
        Schema::dropIfExists('delivery_addresses');
    }
};
