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
        Schema::create('absences', function (Blueprint $table) {
    $table->id();

    $table->foreignId('eleve_id')
        ->constrained('eleves')
        ->onDelete('cascade');

    $table->foreignId('trajet_id')
        ->constrained('trajets')
        ->onDelete('cascade');

    $table->date('date_absence');
    $table->string('raison')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropForeign(['eleve_id']);
            $table->dropForeign(['trajet_id']);
        });
        Schema::dropIfExists('absences');
    }
};
