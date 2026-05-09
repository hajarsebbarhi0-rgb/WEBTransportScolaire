<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'trajet_id',
        'date_absence',
        'raison',
         'periode',
         'status'
    ];

    // Une absence appartient à un élève
    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    // Une absence appartient à un trajet
    public function trajet()
    {
        return $this->belongsTo(Trajet::class, 'trajet_id');
    }
}
