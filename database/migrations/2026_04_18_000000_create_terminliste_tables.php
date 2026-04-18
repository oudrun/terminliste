<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dog_trials', function (Blueprint $table): void {
            $table->id();
            $table->string('trial_number')->unique();
            $table->string('title');
            $table->string('organizer');
            $table->string('location');
            $table->string('trial_place')->default('');
            $table->date('date_start');
            $table->date('date_end');
            $table->date('registration_deadline');
            $table->text('description');
            $table->string('contact_person')->default('');
            $table->string('contact_phone')->default('');
            $table->string('contact_email')->default('');
            $table->string('responsible_club')->default('');
            $table->timestamps();
        });

        Schema::create('trial_classes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('trial_id')->constrained('dog_trials')->cascadeOnDelete();
            $table->string('name');
            $table->time('start_time')->nullable();
            $table->string('judge')->default('');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('max_participants')->default(10);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->constrained('trial_classes')->cascadeOnDelete();
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone')->default('');
            $table->string('dog_name');
            $table->string('dog_regno')->default('');
            $table->string('dog_breed')->default('');
            $table->string('dog_class')->default('');
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('trial_classes');
        Schema::dropIfExists('dog_trials');
    }
};
