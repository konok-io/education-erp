<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_visitors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('visitor_name', 150);
            $table->string('relation', 100)->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('hostel_buildings')->nullOnDelete();
            $table->string('id_proof', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamp('entry_time')->useCurrent();
            $table->timestamp('exit_time')->nullable();
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'entry_time']);
            $table->index('entry_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_visitors');
    }
};
