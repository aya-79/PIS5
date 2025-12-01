<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enseignant;
use Illuminate\Support\Facades\Hash;

class EnseignantAuthController extends Controller
{
    // Inscription
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:enseignants,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $enseignant = Enseignant::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $enseignant->createToken('api_token')->plainTextToken;

        return response()->json([
            'enseignant' => $enseignant,
            'token' => $token
        ], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $enseignant = Enseignant::where('email', $request->email)->first();

        if (!$enseignant || !Hash::check($request->password, $enseignant->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $token = $enseignant->createToken('api_token')->plainTextToken;

        return response()->json([
            'enseignant' => $enseignant,
            'token' => $token
        ]);
    }
}
