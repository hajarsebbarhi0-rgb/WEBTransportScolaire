<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trajets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->time('debut');
$table->time('fin');

            $table->string('status')->default('en_attente');
            $table->timestamps();

            // Clé étrangère pour le transport (le bus)
            $table->foreignId('transport_id')->constrained('transports')->onDelete('cascade');

            // Clé étrangère pour le chauffeur
            $table->foreignId('chauffeur_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajets', function (Blueprint $table) {
            // Assurez-vous de bien spécifier le nom de la clé étrangère si elle existe.
            // La convention de nommage par défaut est `table_colonne_foreign`
            $table->dropForeign(['transport_id']);
            $table->dropForeign(['chauffeur_id']);
        });

        Schema::dropIfExists('trajets');
    }
};
