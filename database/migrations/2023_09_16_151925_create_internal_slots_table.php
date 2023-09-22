<?php

use App\Services\GlobalStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internal_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('field_id');
            $table->time('time');
            $table->string(
                'record_status',
                20
            )->default(GlobalStatus::getRecordStatus('Active'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_slots');
    }
};
