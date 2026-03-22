<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('badge_section_label')->nullable();
            $table->string('badge_section_title')->nullable();
            $table->string('badge_section_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['badge_section_label', 'badge_section_title', 'badge_section_text']);
        });
    }
};
