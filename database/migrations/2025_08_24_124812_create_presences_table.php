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
       Schema::create('presences', function (Blueprint $table) {
    $table->id();

    $table->foreignId('eleve_id')
        ->constrained('eleves')
        ->onDelete('cascade');

    $table->foreignId('trajet_id')
        ->constrained('trajets')
        ->onDelete('cascade');

    $table->boolean('status'); // true = présent
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
        Schema::dropIfExists('presences');
    }
};
