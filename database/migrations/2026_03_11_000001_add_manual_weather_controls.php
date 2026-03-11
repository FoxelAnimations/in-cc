<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('rain_mode', 20)->default('automatic')->after('weather_enabled');
            $table->unsignedTinyInteger('manual_rain_intensity')->default(50)->after('rain_mode');
            $table->unsignedTinyInteger('manual_cloud_cover')->default(50)->after('manual_rain_intensity');
            $table->unsignedTinyInteger('manual_wind_speed')->default(50)->after('manual_cloud_cover');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['rain_mode', 'manual_rain_intensity', 'manual_cloud_cover', 'manual_wind_speed']);
        });
    }
};
