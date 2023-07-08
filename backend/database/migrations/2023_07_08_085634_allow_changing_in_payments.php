<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('income', 'expense', 'exchange')");;

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId("source_account_id")->after("account_id")->nullable()->constrained()->references("id")->on("accounts")->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('income', 'expense')");
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId("source_account_id");
        });
    }
};
