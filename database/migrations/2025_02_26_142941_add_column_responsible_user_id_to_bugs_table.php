<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->foreignId('responsible_user_id')->nullable()->constrained('users');
        });

        DB::table('bugs')->update(['responsible_user_id' => 6]);

        Schema::table('bugs', function (Blueprint $table) {
            $table->foreignId('responsible_user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->dropForeign('bugs_responsible_user_id_foreign');
            $table->dropColumn('responsible_user_id');
        });
    }
};
