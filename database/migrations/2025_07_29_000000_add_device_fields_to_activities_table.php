<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('department', 100)->nullable()->after('reporter_phone');
            $table->string('sub_category', 100)->nullable()->after('category');
            $table->string('device_type', 100)->nullable()->after('sub_category');
            $table->string('barcode_number', 100)->nullable()->after('device_type');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['department', 'sub_category', 'device_type', 'barcode_number']);
        });
    }
};
