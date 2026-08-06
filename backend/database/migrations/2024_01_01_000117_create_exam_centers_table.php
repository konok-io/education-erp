<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_centers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('center_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('building', 150)->nullable();
            $table->string('floor', 50)->nullable();
            $table->text('address')->nullable();
            $table->integer('capacity')->default(40);
            $table->integer('current_capacity')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_centers');
    }
};
