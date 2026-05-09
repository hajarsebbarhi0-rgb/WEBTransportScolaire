<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'trajet_id',
        'latitude',
        'longitude',
        'is_active',

    ];
 public $timestamps = true;
    // La position appartient à un trajet
    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }
}
