<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
        Schema::create('teletravailleurs', function (Blueprint $table) {
            $table->id();
            $table->string('CIN')->nullable()->unique();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('photoProfil')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('teletravailleurs');
    }
};

