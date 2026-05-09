<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arret extends Model
{
    use HasFactory;

    protected $fillable = [
        'trajet_id',
        'nom',
        'latitude',
        'longitude',
        'order_number',
        'is_reached',
    ];

    // 🔹 Un arrêt appartient à un trajet
    public function trajet()
    {
        return $this->belongsTo(Trajet::class, 'trajet_id');
    }

    // 🔹 Un arrêt peut avoir plusieurs élèves qui l’ont choisi
    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'arret_id');
    }
}
 