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
    Schema::table('eleves', function (Blueprint $table) {
        $table->text('adresse')->nullable()->change();
    });
}

public function down()
{
    Schema::table('eleves', function (Blueprint $table) {
        $table->string('adresse', 255)->nullable()->change();
    });
}
};
