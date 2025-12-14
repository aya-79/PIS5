<?php

namespace Database\Seeders;

use App\Models\AgentScolarite as ModelsAgentScolarite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AgentScolarite extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $numeroBase = 100;        
        $agent_scolarites = User::where('role', 'AGENT_SCOLARITE')->get();

        foreach ($agent_scolarites as $user) {
            ModelsAgentScolarite::create([
                'user_id' => $user->id,
                'numero_agent' => $numeroBase++,
            ]);
        }

    }
}
