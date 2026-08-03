<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->uuid('sparepart_id')->nullable()->after('repair_data');
            $table->integer('sparepart_quantity')->nullable()->after('sparepart_id');
            $table->decimal('sparepart_price', 12, 2)->nullable()->after('sparepart_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['sparepart_id', 'sparepart_quantity', 'sparepart_price']);
        });
    }
};
