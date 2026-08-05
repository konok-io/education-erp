<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('event_number')->unique();
            $table->string('event_title');
            $table->text('description')->nullable();
            $table->string('event_type', 50);
            $table->string('banner_image')->nullable();
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->text('agenda')->nullable();
            $table->text('speakers')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('registered_count')->default(0);
            $table->decimal('registration_fee', 10, 2)->nullable();
            $table->boolean('is_free')->default(true);
            $table->boolean('is_virtual')->default(false);
            $table->string('meeting_link')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 50)->default('draft');
            $table->foreignId('organized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('event_number');
            $table->index('event_type');
            $table->index('event_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_events');
    }
};
