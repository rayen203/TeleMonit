<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teletravailleurs', function (Blueprint $table) {
            if (!Schema::hasColumn('teletravailleurs', 'token')) {
                $table->string('token')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teletravailleurs', function (Blueprint $table) {
            if (Schema::hasColumn('teletravailleurs', 'token')) {
                $table->dropColumn('token');
            }
        });
    }
};
