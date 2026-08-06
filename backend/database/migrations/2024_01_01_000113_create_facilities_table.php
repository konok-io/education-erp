<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->foreignId('facility_type_id')->nullable()->constrained('facility_types')->nullOnDelete();
            $table->string('code', 50)->unique();
            $table->string('location', 200)->nullable();
            $table->integer('capacity')->default(0);
            $table->json('equipment')->nullable();
            $table->time('available_from')->default('08:00:00');
            $table->time('available_to')->default('20:00:00');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['available', 'maintenance', 'unavailable'])->default('available');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
