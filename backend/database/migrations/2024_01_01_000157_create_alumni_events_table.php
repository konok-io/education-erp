<?php

declare(strict_types=1);

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
            $table->string('event_code', 50)->unique();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->enum('event_type', ['reunion', 'seminar', 'workshop', 'networking', 'donation_campaign', 'cultural', 'sports', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue', 200)->nullable();
            $table->text('address')->nullable();
            $table->integer('max_attendees')->default(0);
            $table->integer('registered_count')->default(0);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->string('banner_image')->nullable();
            $table->string('organizer', 150)->nullable();
            $table->text('agenda')->nullable();
            $table->text('speakers')->nullable();
            $table->enum('status', ['draft', 'registration_open', 'registration_closed', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_events');
    }
};
