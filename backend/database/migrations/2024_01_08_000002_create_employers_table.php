<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('company_name');
            $table->string('company_code')->unique();
            $table->string('industry');
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('contact_person');
            $table->string('contact_designation')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('company_size', 50)->nullable();
            $table->string('company_type', 50)->nullable();
            $table->string('founded_year')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('status', 50)->default('active');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('company_name');
            $table->index('company_code');
            $table->index('industry');
            $table->index('is_verified');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
