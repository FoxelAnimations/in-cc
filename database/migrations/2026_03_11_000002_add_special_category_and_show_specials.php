<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand the category enum to include 'special'
        DB::statement("ALTER TABLE episodes MODIFY COLUMN category ENUM('episode', 'short', 'mini', 'special') DEFAULT 'episode'");

        // Add show_specials toggle to site_settings
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('show_specials')->default(true)->after('show_minis');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE episodes MODIFY COLUMN category ENUM('episode', 'short', 'mini') DEFAULT 'episode'");

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('show_specials');
        });
    }
};
