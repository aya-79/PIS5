<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    protected $table = 'enseignants';
    
    protected $fillable = [
        'user_id',
        'total_heures_annee',
        'nombre_sessions_realisees',
    ];
    
    protected $casts = [
        'total_heures_annee' => 'float',
        'nombre_sessions_realisees' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'enseignant_matiere');
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }
}
