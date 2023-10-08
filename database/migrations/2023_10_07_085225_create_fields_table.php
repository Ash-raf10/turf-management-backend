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
        Schema::create('fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('turf_id')->nullable()->references('id')->on('turfs')
                ->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('field_type', 30);
            $table->mediumText('description')->nullable();
            $table->string(
                'record_status',
                20
            )->default(GlobalStatus::getRecordStatus('Active'));
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
