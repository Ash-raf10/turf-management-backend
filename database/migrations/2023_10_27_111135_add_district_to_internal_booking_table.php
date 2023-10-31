<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('internal_bookings', function (Blueprint $table) {
            $table->string('district', 100)->after('time');
            $table->string('field_type', 30)->after('district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_bookings', function (Blueprint $table) {
            $table->dropColumn(['district', 'field_type']);
        });
    }
};
