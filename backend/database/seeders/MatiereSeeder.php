<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $matieres = [
            [
                'code' => 'INF101',
                'nom' => 'Programmation Web',
                'description' => 'HTML, CSS, JavaScript, PHP',
                'niveau' => 'L1',
                'credit' => 3,
                'filiere' => 'tronc commun',
                'semestre' => 'S1',
                'nombre_heures_prevu' => 45.0,
            ],
            [
                'code' => 'INF102',
                'nom' => 'Base de données',
                'description' => 'SQL, MySQL, PostgreSQL',
                'niveau' => 'L1',
                'credit' => 2,
                'filiere' => 'tronc commun',
                'semestre' => 'S1',
                'nombre_heures_prevu' => 40.0,
            ],
            [
                'code' => 'INF201',
                'nom' => 'POO avec Java',
                'description' => 'Programmation Orientée Objet',
                'niveau' => 'L2',
                'credit' => 3,
                'filiere' => 'tronc commun',
                'semestre' => 'S3',
                'nombre_heures_prevu' => 48.0,
            ],
            [
                'code' => 'DSI101',
                'nom' => 'Base de données avancée',
                'description' => 'Oracle',
                'niveau' => 'L2',
                'credit' => 3,
                'filiere' => 'DSI',
                'semestre' => 'S3',
                'nombre_heures_prevu' => 40.0,
            ],
            [
                'code' => 'RSS101',
                'nom' => 'Reseaux Avancee',
                'description' => 'Reseaux',
                'niveau' => 'L2',
                'credit' => 3,
                'filiere' => 'RSS',
                'semestre' => 'S2',
                'nombre_heures_prevu' => 42.0,
            ],
        ];

        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
}