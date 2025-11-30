<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enseignant;
use App\Models\Matiere;

class EnseignantMatiereSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les enseignants et matières
        $enseignants = Enseignant::all();
        $matieres = Matiere::all();

        // Si vous avez des données
        if ($enseignants->count() > 0 && $matieres->count() > 0) {
            
            $moussa = Enseignant::whereHas('user', function($q) {
                $q->where('email', 'Moussa@supnum.mr');
            })->first();

            if ($moussa) {
                $moussa->matieres()->attach([
                    Matiere::where('code', 'INF101')->first()->id,
                    Matiere::where('code', 'INF102')->first()->id,
                ]);
            }

            $kaber = Enseignant::whereHas('user', function($q) {
                $q->where('email', 'Kaber@supnum.mr');
            })->first();

            if ($kaber) {
                $kaber->matieres()->attach([
                    Matiere::where('code', 'INF201')->first()->id,
                    Matiere::where('code', 'DSI101')->first()->id,

                ]);
            }

            $tourad = Enseignant::whereHas('user', function($q) {
                $q->where('email', 'Tourad@supnum.mr');
            })->first();

            if ($tourad) {
                $tourad->matieres()->attach([
                    Matiere::where('code', 'RSS101')->first()->id,
                ]);
            }
        }
    }
}