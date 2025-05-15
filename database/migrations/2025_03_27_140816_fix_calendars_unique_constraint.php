<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCalendarsUniqueConstraint extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            $indexExists = DB::select("
                SELECT name FROM sqlite_master
                WHERE type = 'index' AND name = 'calendars_user_id_date_unique'
            ");

            if (empty($indexExists)) {
                Schema::table('calendars', function (Blueprint $table) {
                    $table->unique(['user_id', 'date']);
                });
            }
        } else {
            // Pour les autres bases de données (MySQL, PostgreSQL), ajouter sans vérification
            Schema::table('calendars', function (Blueprint $table) {
                $table->unique(['user_id', 'date']);
            });
        }
    }

    public function down()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'date']);
        });
    }
}
