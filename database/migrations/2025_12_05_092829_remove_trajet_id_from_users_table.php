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
    Schema::table('users', function (Blueprint $table) {
        // 1. Supprimer la clé étrangère si elle existe
        $table->dropForeign(['trajet_id']);

        // 2. Supprimer la colonne
        $table->dropColumn('trajet_id');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // Pour rollback
        $table->unsignedBigInteger('trajet_id')->nullable();
        $table->foreign('trajet_id')->references('id')->on('trajets')->onDelete('cascade');
    });
}

};
