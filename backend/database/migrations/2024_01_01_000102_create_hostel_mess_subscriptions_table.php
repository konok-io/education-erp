<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_mess_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('subscription_no', 50)->unique();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreignId('building_id')->nullable()->constrained('hostel_buildings')->nullOnDelete();
            $table->foreignId('mess_plan_id')->nullable()->constrained('hostel_mess_plans')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->enum('subscription_type', ['monthly', 'weekly', 'daily', 'custom'])->default('monthly');
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_mess_subscriptions');
    }
};
