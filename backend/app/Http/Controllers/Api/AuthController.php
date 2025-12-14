<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enseignant;
use App\Models\AgentScolarite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Map incoming role (front) -> role stored in DB (CHECK constraint).
     */
    private function normalizeRole(?string $role): string
    {
        $role = $role ?? 'enseignant';

        $map = [
            // Frontend values
            'enseignant'     => 'ENSEIGNANT',
            'agent-scolaire' => 'AGENT_SCOLARITE',
            'admin'          => 'ADMINISTRATEUR',

            // If frontend ever sends DB values already
            'ENSEIGNANT'        => 'ENSEIGNANT',
            'AGENT_SCOLARITE'   => 'AGENT_SCOLARITE',
            'ADMINISTRATEUR'    => 'ADMINISTRATEUR',
        ];

        return $map[$role] ?? 'ENSEIGNANT';
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(Request $request)
    {
        try {
            // Convert empty string to null (avoids DB checks / formatting issues)
            $request->merge([
                'telephone' => $request->input('telephone') === '' ? null : $request->input('telephone'),
            ]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                // confirmed requires password_confirmation field :contentReference[oaicite:1]{index=1}
                'password' => 'required|string|min:8|confirmed',
                'telephone' => 'nullable|string|max:20',

                // Accept both front + DB formats (robust), then normalize
                'role' => 'nullable|string|in:enseignant,agent-scolaire,admin,ENSEIGNANT,AGENT_SCOLARITE,ADMINISTRATEUR',
            ]);

            $roleDb = $this->normalizeRole($validated['role'] ?? null);

            $result = DB::transaction(function () use ($validated, $roleDb) {
                $user = User::create([
                    'name' => $validated['name'],
                    'prenom' => $validated['prenom'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'telephone' => $validated['telephone'] ?? null,
                    'role' => $roleDb,
                ]);

                // Créer le profil selon le rôle DB
                if ($roleDb === 'ENSEIGNANT') {
                    $profile = Enseignant::create([
                        'user_id' => $user->id,
                        'total_heures_annee' => 0,
                        'nombre_sessions_realisees' => 0,
                    ]);
                } elseif ($roleDb === 'AGENT_SCOLARITE') {
                    $profile = AgentScolarite::create([
                        'user_id' => $user->id,
                    ]);
                } else {
                    $profile = null; // ADMINISTRATEUR
                }

                $token = $user->createToken('auth_token')->plainTextToken;

                return compact('user', 'profile', 'token');
            });

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'data' => [
                    'user' => $result['user'],
                    'profile' => $result['profile'],
                    'access_token' => $result['token'],
                    'token_type' => 'Bearer',
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Les identifiants fournis sont incorrects.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Match DB role values (NOT frontend labels)
        if ($user->role === 'ENSEIGNANT') {
            $user->load('enseignant.matieres');
            $profile = $user->enseignant;
        } elseif ($user->role === 'AGENT_SCOLARITE') {
            $user->load('agentScolaire');
            $profile = $user->agentScolaire;
        } else {
            $profile = null; // ADMINISTRATEUR
        }

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => $user,
                'profile' => $profile,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Utilisateur connecté
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'ENSEIGNANT') {
            $user->load(['enseignant.matieres', 'enseignant.pointages']);
            $profile = $user->enseignant;
        } elseif ($user->role === 'AGENT_SCOLARITE') {
            $user->load('agentScolaire');
            $profile = $user->agentScolaire;
        } else {
            $profile = null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile' => $profile,
            ]
        ], 200);
    }
}
