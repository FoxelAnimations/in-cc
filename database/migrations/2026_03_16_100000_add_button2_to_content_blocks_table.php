<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            $table->string('button2_label')->nullable()->after('button_new_tab');
            $table->string('button2_url', 500)->nullable()->after('button2_label');
            $table->boolean('button2_new_tab')->default(false)->after('button2_url');
        });
    }

    public function down(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            $table->dropColumn(['button2_label', 'button2_url', 'button2_new_tab']);
        });
    }
};
