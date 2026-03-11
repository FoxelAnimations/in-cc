<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collabs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('featured_image')->nullable();
            $table->string('logo_image')->nullable();
            $table->longText('content')->nullable();
            $table->foreignId('episode_id')->nullable()->constrained('episodes')->nullOnDelete();
            $table->string('link1_label')->nullable();
            $table->string('link1_url', 500)->nullable();
            $table->boolean('link1_new_tab')->default(false);
            $table->string('link2_label')->nullable();
            $table->string('link2_url', 500)->nullable();
            $table->boolean('link2_new_tab')->default(false);
            $table->foreignId('character_id')->nullable()->constrained('characters')->nullOnDelete();
            $table->boolean('show_on_homepage')->default(false);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Add show_collabs to site_settings
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('show_collabs')->default(false)->after('show_specials');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collabs');

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('show_collabs');
        });
    }
};
