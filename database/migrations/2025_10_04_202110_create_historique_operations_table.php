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
    Schema::create('historique_operations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a fait l’action
        $table->string('action'); // ex: 'création', 'modification', 'suppression'
        $table->string('model_type'); // ex: 'Transport', 'Trajet', 'User'
        $table->unsignedBigInteger('model_id')->nullable(); // l'ID de l’objet modifié
        $table->text('description')->nullable(); // Détails (ex: "Transport 'Peugeot 308' créé")
        $table->ipAddress('ip_address')->nullable(); // Optionnel : adresse IP
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
        Schema::dropIfExists('historique_operations');
    }
};
