<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->json('tacheList');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->unique(['user_id', 'date']);
            $table->timestamps();
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
