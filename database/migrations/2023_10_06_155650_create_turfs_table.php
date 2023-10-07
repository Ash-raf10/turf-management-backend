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
        Schema::create('turfs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->nullable()->references('id')->on('companies')
                ->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('email')->unique()->nullable();
            $table->string('mobile', 20)->unique();
            $table->string('address', 500);
            $table->string('district', 100);
            $table->mediumText('description')->nullable();
            $table->string('fb_page', 500)->unique();
            $table->string('website', 500)->nullable();
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
        Schema::dropIfExists('turfs');
    }
};
