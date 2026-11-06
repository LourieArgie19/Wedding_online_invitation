<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('invitation_profile')->nullable()->unique();
            $table->date('wedding_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropUnique(['invitation_profile']);
            $table->dropColumn(['invitation_profile', 'wedding_date']);
        });
    }
};
