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
        Schema::table('order_notification_logs', function (Blueprint $table) {
            $table->boolean("attached")->default(0)->after("channel");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_notification_logs', function (Blueprint $table) {
            $table->dropColumn("attached");
        });
    }
};
