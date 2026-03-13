<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_video_character', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_video_id')->constrained()->cascadeOnDelete();
            $table->foreignId('character_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['camera_video_id', 'character_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_video_character');
    }
};
