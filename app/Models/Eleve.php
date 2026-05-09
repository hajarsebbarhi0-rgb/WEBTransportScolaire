<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'user_id',
        'date_de_naissance',
        'genre',
        'ecole',
        'niveau',
        'photo',
        'trajet_id',   
        'arret_id',
        'adresse',    
    ];

    // 🔹 Un élève appartient à un utilisateur (compte de base)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔹 Un élève appartient à un trajet
    public function trajet()
    {
        return $this->belongsTo(Trajet::class, 'trajet_id');
    }

    // 🔹 Un élève appartient à un arrêt
    public function arret()
    {
        return $this->belongsTo(Arret::class, 'arret_id');
    }

    // 🔹 Un élève a plusieurs absences
    public function absences()
    {
        return $this->hasMany(Absence::class, 'eleve_id');
    }
    
    // 🔹 Un élève a plusieurs présences
    public function presences()
    {
        return $this->hasMany(Presence::class, 'eleve_id');
    }
    public function elevesTousParTrajet()
{
    $user = Auth::user();

    if ($user->role !== 'chauffeur') {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }

    $trajet = Trajet::where('chauffeur_id', $user->id)
        ->with(['eleves.user', 'eleves.absences' => function ($q) {
            $q->whereDate('date_absence', today());
        }])
        ->first();

    if (!$trajet) {
        return response()->json(['message' => 'Aucun trajet trouvé'], 404);
    }

    $eleves = $trajet->eleves->map(function ($eleve) {
        return [
            'id' => $eleve->id,
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'absent_today' => $eleve->absences->isNotEmpty(),
        ];
    });

    return response()->json($eleves);
}

}
