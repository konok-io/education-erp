<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('award_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->decimal('default_reward', 12, 2)->nullable();
            $table->boolean('is_monetary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_awards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('award_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('award_type_id')->constrained('award_types')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->date('award_date');
            $table->text('reason')->nullable();
            $table->decimal('reward_amount', 12, 2)->nullable();
            $table->string('reward_type')->nullable(); // cash, certificate, trophy, plaque
            $table->string('certificate_number')->nullable();
            $table->date('certificate_date')->nullable();
            $table->string('certificate_file')->nullable();
            $table->foreignId('presented_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'award_date']);
            $table->index('award_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_awards');
        Schema::dropIfExists('award_types');
    }
};
