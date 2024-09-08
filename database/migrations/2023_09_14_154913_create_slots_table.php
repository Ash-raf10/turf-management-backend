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
        Schema::create('slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('field_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('type');
            $table->integer('discount')->default(0);
            $table->integer('price');
            $table->string(
                'record_status',
                20
            )->default(GlobalStatus::getRecordStatus('Active'));
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')
                ->cascadeOnDelete();
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
