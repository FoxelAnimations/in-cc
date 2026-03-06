<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camera_videos', function (Blueprint $table) {
            $table->enum('behaviour_type', ['loop', 'realtime'])->default('loop')->after('audio_path');
            $table->unsignedInteger('duration_seconds')->nullable()->after('behaviour_type');
        });
    }

    public function down(): void
    {
        Schema::table('camera_videos', function (Blueprint $table) {
            $table->dropColumn(['behaviour_type', 'duration_seconds']);
        });
    }
};
