<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fundraising_campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('campaign_code')->unique();
            $table->string('campaign_title');
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->decimal('goal_amount', 14, 2);
            $table->decimal('raised_amount', 14, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('fund_category', 100)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('donor_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 50)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('campaign_code');
            $table->index('fund_category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fundraising_campaigns');
    }
};
