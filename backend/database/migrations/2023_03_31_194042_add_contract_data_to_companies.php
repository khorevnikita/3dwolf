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
        Schema::table('customers', function (Blueprint $table) {
            $table->string("full_name")->nullable();
            $table->string("director_title")->nullable();
            $table->string("director_for_contract")->nullable();
            $table->string("legal_statement")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn("full_name");
            $table->dropColumn("director_title");
            $table->dropColumn("director_for_contract");
            $table->dropColumn("legal_statement");
        });
    }
};
