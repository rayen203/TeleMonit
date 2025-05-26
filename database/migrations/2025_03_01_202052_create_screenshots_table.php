<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
        Schema::create('screenshots', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->unsignedBigInteger('teletravailleur_id');
            $table->foreign('teletravailleur_id')->references('id')->on('teletravailleurs')->onDelete('cascade');
            $table->timestamps();

        });
    }



    public function down(): void
    {
        Schema::dropIfExists('screenshots');
    }
};
