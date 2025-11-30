<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enseignant;
use App\Models\User;

class EnseignantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enseignants = User::where('role', 'ENSEIGNANT')->get();

        foreach ($enseignants as $user) {
            Enseignant::create([
                'user_id' => $user->id,
            ]);
        }
    }
}