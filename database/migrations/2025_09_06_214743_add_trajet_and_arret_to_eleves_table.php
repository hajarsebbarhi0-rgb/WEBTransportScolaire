<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // Ajouter la colonne trajet_id
            $table->unsignedBigInteger('trajet_id')->nullable()->after('user_id');
            $table->foreign('trajet_id')
                  ->references('id')
                  ->on('trajets')
                  ->onDelete('set null');

            // Ajouter la colonne arret_id
            $table->unsignedBigInteger('arret_id')->nullable()->after('trajet_id');
            $table->foreign('arret_id')
                  ->references('id')
                  ->on('arrets')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropForeign(['trajet_id']);
            $table->dropForeign(['arret_id']);
            $table->dropColumn(['trajet_id', 'arret_id']);
        });
    }
};
