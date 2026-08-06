<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->string('type'); // counter, gauge, histogram, summary
            $table->decimal('value', 20, 6);
            $table->string('unit')->nullable();
            $table->timestamp('timestamp');
            $table->json('labels')->nullable();
            $table->json('tags')->nullable();
            $table->string('environment')->default('production');
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('cascade');
            $table->index(['service_id', 'name', 'timestamp']);
            $table->index(['name', 'timestamp']);
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_metrics');
    }
};
