<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camera_slot_settings', function (Blueprint $table) {
            $table->string('bg_color', 7)->default('#000000')->after('end_time');
            $table->string('overlay_color', 9)->default('#00000000')->after('bg_color');
        });

        // Seed defaults for existing rows
        DB::table('camera_slot_settings')
            ->where('slot_key', 'nacht')
            ->update(['bg_color' => '#0B1026', 'overlay_color' => '#0000001A']);

        DB::table('camera_slot_settings')
            ->where('slot_key', 'ochtend')
            ->update(['bg_color' => '#F4845F', 'overlay_color' => '#FF8C0030']);

        DB::table('camera_slot_settings')
            ->where('slot_key', 'dag')
            ->update(['bg_color' => '#87CEEB', 'overlay_color' => '#FFFFFF10']);

        DB::table('camera_slot_settings')
            ->where('slot_key', 'avond')
            ->update(['bg_color' => '#D4621A', 'overlay_color' => '#FF450030']);
    }

    public function down(): void
    {
        Schema::table('camera_slot_settings', function (Blueprint $table) {
            $table->dropColumn(['bg_color', 'overlay_color']);
        });
    }
};
