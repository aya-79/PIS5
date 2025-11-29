<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    protected $table='pointages';

    protected $fillable = [
        'date',
        'heure_debut',
        'heure_fin',
        'type_seance',
        'statut_pointage',
        'motif_rejet',
        'date_creation',
        'date_validation',
        'enseignant_id',
        'matiere_id',
        'annee_scolaire_id',
        'validateur_id',
    ];

    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'duree' => 'float',
        'date_creation' => 'datetime',
        'date_validation' => 'datetime',
    ];


    public function getDureeAttribute()
    {
        if ($this->heure_debut && $this->heure_fin) {
            return round(($this->heure_fin->diffInMinutes($this->heure_debut)) / 60, 2);
        }
        return null;
    }

    // Relation avec Enseignant
    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    // Relation avec Matiere
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    // Relation avec AnneeScolaire
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    // Relation avec Validateur (AgentScolarite)
    public function validateur()
    {
        return $this->belongsTo(AgentScolarite::class, 'validateur_id');
    }
}
