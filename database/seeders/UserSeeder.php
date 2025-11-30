<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;  
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Moussa',
                'prenom' => 'supnum',
                'email' => 'Moussa@supnum.mr',
                'telephone' => '22334455',
                'password' => Hash::make('12345678'),  
                'role' => 'ENSEIGNANT'
            ],
            [
                'name' => 'Kaber',
                'prenom' => 'supnum',
                'email' => 'Kaber@supnum.mr',
                'telephone' => '22334456',
                'password' => Hash::make('12345678'),  
                'role' => 'ENSEIGNANT'
            ],
            [
                'name' => 'Tourad',
                'prenom' => 'supnum',
                'email' => 'Tourad@supnum.mr',
                'telephone' => '22334457',
                'password' => Hash::make('12345678'),  
                'role' => 'ENSEIGNANT'
            ],
            [
                'name' => 'Med Mahmoud',
                'prenom' => 'supnum',
                'email' => 'MedMahmoud@supnum.mr',
                'telephone' => '22334458',
                'password' => Hash::make('12345678'),  
                'role' => 'AGENT_SCOLARITE'
            ],
            [
                'name' => 'Oum lmoumnin',
                'prenom' => 'supnum',
                'email' => 'Oumlmoumnin@supnum.mr',
                'telephone' => '22334459',
                'password' => Hash::make('12345678'),  
                'role' => 'AGENT_SCOLARITE'
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}