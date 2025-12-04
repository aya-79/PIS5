<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pointage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

class DashboardEnseignantController extends Controller
{
    /**
     * Affiche le tableau de bord de l'enseignant connecté
     */
    public function index(Request $request)
{
    // 1️⃣ Récupérer l'utilisateur et l'enseignant connecté
    $user = auth()->user();
    $enseignant = $user->enseignant;

    // Vérifier si l'enseignant existe
    if (!$enseignant) {
        return response()->json(['error' => 'Profil enseignant non trouvé'], 404);
    }

    // 2️⃣ Statistiques générales
    $sessionsValidees = Pointage::where('enseignant_id', $enseignant->id)
        ->where('statut_pointage', 'valide')
        ->get();

    $totalHeuresEffectuees = $sessionsValidees->sum('duree');
    $nombreSessionsRealisees = $sessionsValidees->count();

    // 3️⃣ Cours du jour
    $coursDuJour = Pointage::with('matiere')
        ->where('enseignant_id', $enseignant->id)
        ->whereDate('date', Carbon::today())
        ->get();

    // 4️⃣ Cours planifiés dans la semaine
    $coursSemaine = Pointage::with('matiere')
        ->where('enseignant_id', $enseignant->id)
        ->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->get();

    // 5️⃣ Historique des sessions filtrables
    $query = Pointage::with('matiere')
        ->where('enseignant_id', $enseignant->id)
        ->where('statut_pointage', 'APPROUVE')
        ->orderBy('date', 'desc');

    if ($request->has('date')) {
        $query->whereDate('date', $request->date);
    }
    if ($request->has('matiere_id')) {
        $query->where('matiere_id', $request->matiere_id);
    }

    $historiqueSessions = $query->get();

    // 6️⃣ Historique des pointages (validés, rejetés, en attente)
    $pointages = Pointage::with('matiere')
        ->where('enseignant_id', $enseignant->id)
        ->orderBy('date', 'desc')
        ->get();

    // ➤ Réponse JSON - DONNÉES PRISES DE LA TABLE USERS
    return response()->json([
        'enseignant' => [
            'nom' => $user->name,              // ← De users
            'prenom' => $user->prenom,         // ← De users
            'email' => $user->email,           // ← De users
            'code' => $user->code ?? null,     // ← De users (si existe)
            'telephone' => $user->telephone,   // ← De users
            'role' => $user->role,             // ← De users
        ],

        'stats' => [
            'total_heures_effectuees' => $totalHeuresEffectuees,
            'nombre_sessions_realisees' => $nombreSessionsRealisees,
            'total_heures_annee' => $enseignant->total_heures_annee ?? 0,  // ← De enseignants
        ],

        'cours_du_jour' => $coursDuJour->map(function ($cours) {
            return [
                'date' => $cours->date->format('Y-m-d'),
                'heure_debut' => $cours->heure_debut->format('H:i'),
                'heure_fin' => $cours->heure_fin->format('H:i'),
                'duree' => $cours->duree,
                'matiere' => $cours->matiere->nom,
                'statut_pointage' => $cours->statut_pointage
            ];
        }),

        'cours_semaine' => $coursSemaine->map(function ($cours) {
            return [
                'date' => $cours->date->format('Y-m-d'),
                'heure_debut' => $cours->heure_debut->format('H:i'),
                'heure_fin' => $cours->heure_fin->format('H:i'),
                'matiere' => $cours->matiere->nom,
                'statut_pointage' => $cours->statut_pointage,
            ];
        }),

        'historique_sessions' => $historiqueSessions->map(function ($session) {
            return [
                'date' => $session->date->format('Y-m-d'),
                'heure_debut' => $session->heure_debut->format('H:i'),
                'heure_fin' => $session->heure_fin->format('H:i'),
                'duree' => $session->duree,
                'matiere' => $session->matiere->nom,
                'statut_pointage' => $session->statut_pointage,
            ];
        }),

        'pointages' => $pointages->map(function ($p) {
            return [
                'date' => $p->date->format('Y-m-d'),
                'matiere' => $p->matiere->nom,
                'type_seance' => $p->type_seance,
                'duree' => $p->duree,
                'statut' => $p->statut_pointage,
                'motif_rejet' => $p->motif_rejet
            ];
        }),
    ]);
}
}
