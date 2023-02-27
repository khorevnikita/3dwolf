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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('father_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('telegram')->nullable();
            $table->enum('type', ['individual', 'entity']);
            $table->enum('entity_type', ['self_employed', 'company'])->nullable();
            $table->string('inn')->nullable();
            $table->string('kpp')->nullable();
            $table->string('ogrn')->nullable();
            $table->string('okpo')->nullable();
            $table->string('okved')->nullable();
            $table->text('address')->nullable();
            $table->string('ceo')->nullable();
            $table->string('rs')->nullable();
            $table->string('bank')->nullable();
            $table->string('bik')->nullable();
            $table->string('ks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
