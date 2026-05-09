<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'trajet_id',
        'type',
        'description',
        'created_by',
        'status',
    ];

    // Un incident appartient à un trajet
    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }

    // Un incident a été créé par un utilisateur
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
