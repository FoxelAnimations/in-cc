<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beacon_scans', function (Blueprint $table) {
            $table->decimal('recorded_latitude', 10, 7)->nullable()->after('utm_content');
            $table->decimal('recorded_longitude', 10, 7)->nullable()->after('recorded_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('beacon_scans', function (Blueprint $table) {
            $table->dropColumn(['recorded_latitude', 'recorded_longitude']);
        });
    }
};
