<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'marque',
        'modele',
        'plaque_immatriculation',
        'capacite_passagers',
        'status', 
    ];

    /**
     * Get the trajets for the transport.
     */
    public function trajets()
    {
        return $this->hasMany(Trajet::class);
    }
}
