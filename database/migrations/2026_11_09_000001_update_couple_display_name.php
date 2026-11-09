<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('weddings')
            ->where('invitation_profile', 'dy-ann-dennis')
            ->where('name', 'Dy Ann & Dennis')
            ->update(['name' => 'Dennis & Dy Ann']);
    }

    public function down(): void
    {
        DB::table('weddings')
            ->where('invitation_profile', 'dy-ann-dennis')
            ->where('name', 'Dennis & Dy Ann')
            ->update(['name' => 'Dy Ann & Dennis']);
    }
};
