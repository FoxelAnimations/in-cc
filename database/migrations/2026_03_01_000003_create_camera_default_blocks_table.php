<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_default_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->constrained()->cascadeOnDelete();
            $table->foreignId('camera_video_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('day_of_week'); // 0=Ma, 1=Di, 2=Wo, 3=Do, 4=Vr, 5=Za, 6=Zo
            $table->enum('time_slot', ['nacht', 'ochtend', 'dag', 'avond']);
            $table->timestamps();

            $table->unique(['camera_id', 'day_of_week', 'time_slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_default_blocks');
    }
};
