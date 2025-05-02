<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id(); // id: int [PK]
            $table->date('date');
            $table->json('tacheList'); // tacheList: JSON
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->unique(['user_id', 'date']); // Clé composite unique
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
