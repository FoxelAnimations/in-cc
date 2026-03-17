<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beacons', function (Blueprint $table) {
            $table->boolean('is_collectible')->default(false)->after('is_out_of_action');
            $table->string('badge_image_path')->nullable()->after('is_collectible');
        });

        Schema::create('beacon_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('beacon_id')->constrained()->cascadeOnDelete();
            $table->timestamp('collected_at');
            $table->unique(['user_id', 'beacon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beacon_user');

        Schema::table('beacons', function (Blueprint $table) {
            $table->dropColumn(['is_collectible', 'badge_image_path']);
        });
    }
};
