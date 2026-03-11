<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('show_quotes')->default(true)->after('show_collabs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('show_quotes');
        });
    }
};
