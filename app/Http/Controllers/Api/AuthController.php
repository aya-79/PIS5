<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enseignant;
use App\Models\AgentScolarite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'telephone' => 'nullable|string|max:20',
                'role' => 'nullable|string|in:enseignant,agent-scolaire,admin',
            ]);

            $role = $validated['role'] ?? 'enseignant';

            $user = User::create([
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telephone' => $validated['telephone'] ?? null,
                'role' => $role,
            ]);

            // Créer le profil selon le rôle
            if ($role === 'enseignant') {
                $profile = Enseignant::create([
                    'user_id' => $user->id,
                    'total_heures_annee' => 0,
                    'nombre_sessions_realisees' => 0,
                ]);
            } elseif ($role === 'agent-scolaire') {
                $profile = AgentScolarite::create([
                    'user_id' => $user->id,
                    // ajoute ici d'autres champs spécifiques si nécessaire
                ]);
            } else {
                $profile = null; // admin n'a pas de profil spécifique
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'data' => [
                    'user' => $user,
                    'profile' => $profile,
                    'access_token' => $token,
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

        // Charger le profil selon le rôle
        if ($user->role === 'enseignant') {
            $user->load('enseignant.matieres');
            $profile = $user->enseignant;
        } elseif ($user->role === 'agent-scolaire') {
            $user->load('agentScolaire'); // Relation à définir dans User
            $profile = $user->agentScolaire;
        } else {
            $profile = null; // admin n'a pas de profil spécifique
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

        if ($user->role === 'enseignant') {
            $user->load(['enseignant.matieres', 'enseignant.pointages']);
            $profile = $user->enseignant;
        } elseif ($user->role === 'agent-scolaire') {
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

    // Les autres méthodes (updateProfile, changePassword, logout, logoutAll, statistics) peuvent rester identiques,
    // en adaptant statistics pour prendre en compte agent-scolaire si nécessaire.
}
