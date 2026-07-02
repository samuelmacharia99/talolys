<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('iso_name', 40)->nullable();
            $table->string('continent', 40)->nullable();
            $table->string('currency_code', 40)->nullable();
            $table->string('currency_name', 40)->nullable();
            $table->string('currency_symbol', 40)->nullable();
            $table->string('flag_url')->nullable();
            $table->text('calling_codes')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('operator_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_groups');
        Schema::dropIfExists('countries');
    }
};
