<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_overheads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('gl_account');
            $table->text('gl_name')->nullable();
            $table->date('posting_date')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->text('text')->nullable();
            $table->text('document_header')->nullable();
            $table->string('profit_center')->nullable();
            $table->char('company_code', 10)->nullable();
            $table->string('departemen')->nullable();
            $table->string('user')->nullable();
            $table->string('activity')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_overheads');
    }
};
