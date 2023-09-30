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
        Schema::create('internal_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('internal_slot_id')->references('id')->on('internal_slots')
                ->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('status', GlobalStatus::getBookingStatus());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_bookings');
    }
};
