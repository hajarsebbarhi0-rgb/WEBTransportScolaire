<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Attributs remplissables
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'status',
        'role',
    ];

    /**
     * Attributs masqués
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts automatiques
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // === Relations ===

    // Relation avec Élève (si l’utilisateur est un élève)
    public function eleve()
{
    return $this->hasOne(Eleve::class, 'user_id');
}

   

    // Relation avec les trajets si l’utilisateur est un chauffeur
public function trajets()
{
    return $this->hasMany(Trajet::class, 'chauffeur_id');
}

}
