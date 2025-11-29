<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeScolaire extends Model
{
    
    protected $table = 'annee_scolaires';
    
    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'est_active',
    ];
    
    protected $casts = [
        'date_debut' => 'date',    
        'date_fin' => 'date',
        'est_active' => 'boolean', 
    ];

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }

    public static function getActive()
    {
        return self::where('est_active', true)->first();
    }
}
