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
    Schema::table('presences', function (Blueprint $table) {
        $table->date('date_presence')->after('trajet_id');
        $table->enum('periode', ['matin', 'soir'])->after('date_presence');
    });
}

public function down()
{
    Schema::table('presences', function (Blueprint $table) {
        $table->dropColumn(['date_presence', 'periode']);
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    
};
