<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number', 20)->unique();
            $table->string('title', 255);
            $table->text('description');
            $table->enum('category', ['hardware', 'software', 'network', 'other']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->string('reporter_name', 255);
            $table->string('reporter_phone', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->uuid('assigned_to')->nullable();
            $table->uuid('team_id')->nullable();
            $table->uuid('created_by');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};