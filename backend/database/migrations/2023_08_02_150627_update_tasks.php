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
        Schema::create("task_user", function (Blueprint $table) {
            $table->id();
            $table->foreignId("task_id")->constrained()->cascadeOnDelete();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        $tasks = DB::table("tasks")->whereNotNull("user_id")->get();
        foreach ($tasks as $task) {
            DB::table("task_user")
                ->insert([
                    'task_id' => $task->id,
                    'user_id' => $task->user_id,
                ]);
        }

        Schema::table("tasks", function (Blueprint $table) {
            $table->dropConstrainedForeignId("user_id");
        });

        Schema::table("users", function (Blueprint $table) {
            $table->integer("access_level")->default(0);
        });

        $users = DB::table("users")->get();
        foreach ($users as $user) {
            if ($user->customer_id) {
                continue;
            }
            DB::table("users")
                ->where("id", $user->id)
                ->update([
                    'access_level' => $user->is_admin ? 2 : 1,
                ]);
        }

        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("is_admin");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("task_user");
        Schema::table("task_user", function (Blueprint $table) {
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
        });
        Schema::table("users", function (Blueprint $table) {
            $table->boolean("is_admin")->default(false);
            $table->dropColumn("access_level");
        });
    }
};
