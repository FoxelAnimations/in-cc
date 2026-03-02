<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Reorder: ochtend(0), dag(1), avond(2), nacht(3)
        DB::table('camera_slot_settings')->where('slot_key', 'ochtend')->update(['sort_order' => 0]);
        DB::table('camera_slot_settings')->where('slot_key', 'dag')->update(['sort_order' => 1]);
        DB::table('camera_slot_settings')->where('slot_key', 'avond')->update(['sort_order' => 2]);
        DB::table('camera_slot_settings')->where('slot_key', 'nacht')->update(['sort_order' => 3]);
    }

    public function down(): void
    {
        DB::table('camera_slot_settings')->where('slot_key', 'nacht')->update(['sort_order' => 0]);
        DB::table('camera_slot_settings')->where('slot_key', 'ochtend')->update(['sort_order' => 1]);
        DB::table('camera_slot_settings')->where('slot_key', 'dag')->update(['sort_order' => 2]);
        DB::table('camera_slot_settings')->where('slot_key', 'avond')->update(['sort_order' => 3]);
    }
};
