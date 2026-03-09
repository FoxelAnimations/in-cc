<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_default_sounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->constrained()->cascadeOnDelete();
            $table->string('time_slot'); // ochtend, dag, avond, nacht
            $table->string('sound_path');
            $table->timestamps();

            $table->unique(['camera_id', 'time_slot']);
        });

        // Remove unused column from slot settings
        if (Schema::hasColumn('camera_slot_settings', 'default_sound_path')) {
            Schema::table('camera_slot_settings', function (Blueprint $table) {
                $table->dropColumn('default_sound_path');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_default_sounds');

        Schema::table('camera_slot_settings', function (Blueprint $table) {
            $table->string('default_sound_path')->nullable()->after('wind_enabled');
        });
    }
};
