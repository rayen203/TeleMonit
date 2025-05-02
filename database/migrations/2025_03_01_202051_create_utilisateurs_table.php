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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id(); // id: int [PK]
            $table->string('nom'); // nom: string
            $table->string('prenom'); // prenom: string
            $table->string('email')->unique(); // email: string
            $table->string('password'); // password: string
            $table->timestamp('email_verified_at')->nullable(); // email_verified_at (pour MustVerifyEmail)
            $table->boolean('statut')->default(false)->after('last_activity');
            $table->rememberToken(); // remember_token (pour l’authentification)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
