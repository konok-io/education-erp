<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exit_clearances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('clearance_no', 50)->unique();
            $table->foreignId('employee_exit_id')->constrained('employee_exits')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('clearance_type', [
                'library',
                'accounts',
                'ict',
                'transport',
                'hostel',
                'administration',
                'security',
                'store'
            ]);
            $table->boolean('is_cleared')->default(false);
            $table->date('clearance_date')->nullable();
            $table->foreignId('cleared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->text('pending_items')->nullable();
            $table->decimal('dues_amount', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['employee_exit_id', 'clearance_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exit_clearances');
    }
};
