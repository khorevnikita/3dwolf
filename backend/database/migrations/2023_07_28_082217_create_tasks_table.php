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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->string("name");
            $table->text("description")->nullable();
            $table->dateTime('datetime');
            $table->boolean("completed")->default(false);
            $table->boolean("notified")->default(false);
            $table->timestamps();
        });

        Schema::table("users", function (Blueprint $blueprint) {
            $blueprint->string("tg_username")->nullable();
            $blueprint->string("tg_channel_id")->nullable();
        });

        Schema::table("user_permissions", function (Blueprint $table) {
            $table->boolean("tasks")->after("newsletters")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_permissions", function (Blueprint $blueprint) {
            $blueprint->dropColumn(["tasks"]);
        });
        Schema::table("users", function (Blueprint $blueprint) {
            $blueprint->dropColumn(["tg_username", "tg_channel_id"]);
        });
        Schema::dropIfExists('tasks');
    }
};
