<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   
    public function up()
{
    Schema::table('absences', function (Blueprint $table) {
        $table->enum('periode', ['matin', 'soir'])
              ->after('date_absence');
    });
}

public function down()
{
    Schema::table('absences', function (Blueprint $table) {
        $table->dropColumn('periode');
    });
}

};
