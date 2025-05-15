<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teletravailleur_id')->constrained('teletravailleurs')->onDelete('cascade');
            $table->date('date')->nullable()->default(null);
            $table->timestamp('start_time')->nullable()->default(null);
            $table->timestamp('pause_time')->nullable()->default(null);
            $table->timestamp('resume_time')->nullable()->default(null);
            $table->timestamp('stop_time')->nullable()->default(null);
            $table->bigInteger('total_seconds')->default(0);
            $table->bigInteger('pause_total_seconds')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};

