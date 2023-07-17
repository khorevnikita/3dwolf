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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("responsible_person")->nullable();
            $table->string("phone")->nullable();
            $table->text("address")->nullable();
            $table->text("working_hours")->nullable();
            $table->boolean("is_default")->default(false);
            $table->timestamps();
        });

        Schema::table("orders", function (Blueprint $table) {
            $table->foreignId("branch_id")->nullable()->after("date")->constrained()->nullOnDelete();
        });

        Schema::table("user_permissions", function (Blueprint $table) {
            $table->boolean("branches")->after("customers")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_permissions", function (Blueprint $table) {
            $table->dropColumn("branches");
        });

        Schema::table("orders", function (Blueprint $table) {
            $table->dropConstrainedForeignId("branch_id");
        });

        Schema::dropIfExists('branches');
    }
};
