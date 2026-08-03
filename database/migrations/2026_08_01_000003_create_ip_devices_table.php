<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('brand');
            $table->string('specifications')->nullable();
            $table->string('ip_address');
            $table->string('location');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_devices');
    }
};
