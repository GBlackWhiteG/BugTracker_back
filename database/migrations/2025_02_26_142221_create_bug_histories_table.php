<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bug_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bug_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->enum('field', ['status', 'priority', 'criticality', 'responsible_user_id']);
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_histories');
    }
};
