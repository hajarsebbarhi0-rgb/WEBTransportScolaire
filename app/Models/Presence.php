<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Presence extends Model
{
    use HasFactory;
    protected $fillable = [
        'eleve_id',
        'trajet_id',
        'status',
        'date_presence',
        'periode',
    ];

    // Une présence appartient à un élève
    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }
protected $attributes = [
    'status' => 'active'
];
    // Une présence appartient à un trajet
    public function trajet()
    {
        return $this->belongsTo(Trajet::class, 'trajet_id');
    }
}
