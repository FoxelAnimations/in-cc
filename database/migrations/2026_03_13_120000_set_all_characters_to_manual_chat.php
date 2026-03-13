<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('characters')->update(['chat_mode' => 'manual']);
    }

    public function down(): void
    {
        // No rollback — individual characters can be set back to 'ai' manually
    }
};
