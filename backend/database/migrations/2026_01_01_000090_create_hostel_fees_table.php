<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_fees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('fee_no', 50)->unique();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreignId('building_id')->nullable()->constrained('hostel_buildings')->nullOnDelete();
            $table->string('fee_head', 100);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('waiver', 10, 2)->default(0);
            $table->decimal('paid', 10, 2)->default(0);
            $table->decimal('due', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'waived'])->default('pending');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_fees');
    }
};
