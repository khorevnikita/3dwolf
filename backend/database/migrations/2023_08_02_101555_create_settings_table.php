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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text("brand_name")->nullable();
            $table->text("legal_name")->nullable();
            $table->text("legal_full_name")->nullable();
            $table->string("legal_statement")->nullable();
            $table->string("ogrn")->nullable();
            $table->string("inn")->nullable();
            $table->string("city")->nullable();
            $table->text("address")->nullable();
            $table->text("bank")->nullable();
            $table->string("rs")->nullable();
            $table->string("ks")->nullable();
            $table->string("bik")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->string("website")->nullable();
            $table->string("notification_time")->nullable();
            $table->timestamps();
        });

        Schema::table("user_permissions", function (Blueprint $table) {
            $table->boolean("settings")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_permissions", function (Blueprint $table) {
            $table->dropColumn("settings");
        });

        Schema::dropIfExists('settings');
    }
};
