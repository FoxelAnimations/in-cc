<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_slot_settings', function (Blueprint $table) {
            $table->id();
            $table->string('slot_key')->unique(); // nacht, ochtend, dag, avond
            $table->string('label');
            $table->string('start_time', 5); // HH:MM
            $table->string('end_time', 5);   // HH:MM (24:00 for midnight)
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default values
        DB::table('camera_slot_settings')->insert([
            ['slot_key' => 'nacht',   'label' => 'Nacht',   'start_time' => '00:00', 'end_time' => '06:00', 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['slot_key' => 'ochtend', 'label' => 'Ochtend', 'start_time' => '06:00', 'end_time' => '08:00', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['slot_key' => 'dag',     'label' => 'Dag',     'start_time' => '08:00', 'end_time' => '18:00', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['slot_key' => 'avond',   'label' => 'Avond',   'start_time' => '18:00', 'end_time' => '24:00', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_slot_settings');
    }
};
