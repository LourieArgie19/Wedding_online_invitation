<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('weddings')
            ->where('invitation_profile', 'dy-ann-dennis')
            ->where('hero3', 'dy-ann-dennis/on-the-steps.webp')
            ->update(['hero3' => 'dy-ann-dennis/99f1af15-7e0d-4248-b65c-758297be1463.jpeg']);
    }

    public function down(): void
    {
        DB::table('weddings')
            ->where('invitation_profile', 'dy-ann-dennis')
            ->where('hero3', 'dy-ann-dennis/99f1af15-7e0d-4248-b65c-758297be1463.jpeg')
            ->update(['hero3' => 'dy-ann-dennis/on-the-steps.webp']);
    }
};
