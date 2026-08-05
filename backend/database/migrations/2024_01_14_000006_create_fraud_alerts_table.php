<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('alert_code')->unique();
            $table->string('alert_type', 100);
            $table->string('severity', 50);
            $table->string('status', 50)->default('new');
            $table->string('description');
            $table->string('source', 100)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('related_entity_type')->nullable();
            $table->string('related_entity_id')->nullable();
            $table->text('details')->nullable();
            $table->text('action_taken')->nullable();
            $table->foreignId('investigator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('investigated_at')->nullable();
            $table->timestamps();
            
            $table->index('alert_code');
            $table->index('alert_type');
            $table->index('severity');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_alerts');
    }
};
