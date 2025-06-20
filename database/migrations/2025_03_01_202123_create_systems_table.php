<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->string('setting_name');
            $table->string('setting_value');
            $table->timestamps();
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
