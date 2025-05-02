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
        Schema::create('chatbots', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sessionId'); // session: string
            $table->json('historique')->nullable(); // historique: JSON
            $table->unsignedBigInteger('teletravailleur_id')->unique();
            $table->foreign('teletravailleur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbots');
    }
};
