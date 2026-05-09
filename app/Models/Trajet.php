<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'chauffeur_id',
        'transport_id',
        'debut',
        'fin',
        'status',
        'code_trajet',
    ];

    // 🔹 Un trajet appartient à un chauffeur
    public function chauffeur()
    {
        return $this->belongsTo(User::class, 'chauffeur_id');
    }

    // 🔹 Un trajet appartient à un transport
    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    // 🔹 Un trajet a plusieurs arrêts
    public function arrets()
    {
        return $this->hasMany(Arret::class, 'trajet_id');
    }

    // 🔹 Un trajet a plusieurs élèves
    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'trajet_id');
    }

    // Relations existantes
    public function busPositions()
    {
        return $this->hasMany(BusPosition::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
    
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
