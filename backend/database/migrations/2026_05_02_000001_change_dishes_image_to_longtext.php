<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change image column from TEXT to LONGTEXT to support large base64 images
        // TEXT has a limit of 65,535 bytes, LONGTEXT can store up to 4GB
        DB::statement('ALTER TABLE dishes MODIFY image LONGTEXT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE dishes MODIFY image TEXT');
    }
};