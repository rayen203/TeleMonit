<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCalendarsUniqueConstraint extends Migration
{
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte d'unicité sur user_id, si elle existe
            try {
                $table->dropUnique('calendars_user_id_unique');
            } catch (\Exception $e) {
                // Ignorer l'erreur si la contrainte n'existe pas
            }

            // Ajouter une nouvelle contrainte d'unicité sur user_id et date
            try {
                $table->unique(['user_id', 'date'], 'calendars_user_id_date_unique');
            } catch (\Exception $e) {
                // Ignorer l'erreur si la contrainte existe déjà
            }
        });
    }

    public function down()
    {
        Schema::table('calendars', function (Blueprint $table) {
            // Supprimer la nouvelle contrainte
            $table->dropUnique('calendars_user_id_date_unique');

            // Recréer l'ancienne contrainte sur user_id
            $table->unique('user_id', 'calendars_user_id_unique');
        });
    }
}
