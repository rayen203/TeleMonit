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
        Schema::create('teletravailleurs', function (Blueprint $table) {
            $table->id(); // id: int [PK]
            $table->string('CIN')->nullable()->unique();
            $table->string('telephone')->nullable(); // telephone: string
            $table->string('adresse')->nullable(); // adresse: string (ajouté d’après ton diagramme)
            $table->string('photoProfil')->nullable(); // photoProfil: string
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teletravailleurs');
    }
};

