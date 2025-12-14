<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    
    protected $table = 'matieres';

    protected $fillable = [
        'code',
        'nom',
        'description',
        'niveau',
        'credit',
        'filiere',
        'semestre',
        'nombre_heures_prevu',
    ];
    
    protected $casts = [
        'credit' => 'integer',
        'nombre_heures_prevu' => 'float',
    ];

    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere');
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }

}
