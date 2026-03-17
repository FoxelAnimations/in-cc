<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('episode_ratings', function (Blueprint $table) {
            $table->index('episode_id');
        });
    }

    public function down(): void
    {
        Schema::table('episode_ratings', function (Blueprint $table) {
            $table->dropIndex(['episode_id']);
        });
    }
};
