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
        Schema::create('incidents', function (Blueprint $table) {
           $table->id();
        $table->foreignId('trajet_id')->constrained('trajets')->onDelete('cascade');
        $table->string('type');
        $table->text('description');
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
        $table->string('status')->default('en_attente');
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
        Schema::dropIfExists('incidents');
    }
};
