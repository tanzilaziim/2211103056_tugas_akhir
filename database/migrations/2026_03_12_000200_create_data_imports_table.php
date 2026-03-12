<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_imports', function (Blueprint $table) {
            $table->id();
            $table->string('import_code')->unique();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('storage_path');
            $table->string('file_type', 20)->default('csv');
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->enum('status', ['uploaded', 'processed', 'failed'])->default('uploaded');
            $table->unsignedInteger('row_count')->nullable();
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('data_imports');
    }
};

