<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pointage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PointageController extends Controller
{
    // Lister les pointages de l'enseignant connecté
    public function index(Request $request)
    {
        $user = auth()->user();
        $enseignant = $user->enseignant;

        if (!$enseignant) {
            return response()->json(['error' => 'Enseignant non trouvé'], 404);
        }

        $query = Pointage::with('matiere')
            ->where('enseignant_id', $enseignant->id)
            ->orderBy('date', 'desc');

        // Filtres optionnels
        if ($request->has('statut_pointage')) {
            $query->where('statut_pointage', $request->statut_pointage);
        }

        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date', [$request->date_debut, $request->date_fin]);
        }

        $pointages = $query->get();

        return response()->json([
            'success' => true,
            'data' => $pointages
        ]);
    }

    // Créer un nouveau pointage (enseignant connecté)
    public function store(Request $request)
    {
        $user = auth()->user();
        $enseignant = $user->enseignant;

        if (!$enseignant) {
            return response()->json(['error' => 'Enseignant non trouvé'], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type_seance' => 'required|in:CM,TD,TP',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_scolaire_id' => 'nullable|exists:annee_scolaires,id'
        ]);

         // ✅ Récupérer automatiquement l'année scolaire active
        $anneeScolaire = \App\Models\AnneeScolaire::where('est_active', true)->first();
        
        if (!$anneeScolaire) {
            return response()->json([
                'error' => 'Aucune année scolaire active trouvée'
            ], 400);
        }

        // Calculer la durée
        $debut = \Carbon\Carbon::createFromFormat('H:i', $validated['heure_debut']);
        $fin = \Carbon\Carbon::createFromFormat('H:i', $validated['heure_fin']);
        $duree = $debut->diffInMinutes($fin) / 60;

        $pointage = Pointage::create([
            'enseignant_id' => $enseignant->id, // Toujours l'enseignant connecté
            'date' => $validated['date'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'duree' => $duree,
            'type_seance' => $validated['type_seance'],
            'matiere_id' => $validated['matiere_id'],
            'annee_scolaire_id' => $anneeScolaire->id ,
            // 'statut_pointage' => 'en_attente',
            'date_creation' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pointage créé avec succès. En attente de validation.',
            'data' => $pointage->load('matiere')
        ], 201);
    }

    // Voir un pointage (seulement ses propres pointages)
    public function show($id)
    {
        $user = auth()->user();
        $enseignant = $user->enseignant;

        $pointage = Pointage::with('matiere')
            ->where('enseignant_id', $enseignant->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pointage
        ]);
    }

    // Mettre à jour un pointage (seulement si non validé)
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $enseignant = $user->enseignant;

        $pointage = Pointage::where('enseignant_id', $enseignant->id)
            ->findOrFail($id);

        // Vérifier le statut
        if ($pointage->statut_pointage === 'approuve') {
            return response()->json([
                'error' => 'Impossible de modifier un pointage approuvé'
            ], 403);
        }

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'heure_debut' => 'sometimes|date_format:H:i',
            'heure_fin' => 'sometimes|date_format:H:i',
            'type_seance' => 'sometimes|in:CM,TD,TP',
            'matiere_id' => 'sometimes|exists:matieres,id'
        ]);

        // Recalculer la durée si nécessaire
        if (isset($validated['heure_debut']) || isset($validated['heure_fin'])) {
            $debut = Carbon::parse($validated['heure_debut'] ?? $pointage->heure_debut);
            $fin = Carbon::parse($validated['heure_fin'] ?? $pointage->heure_fin);
            $validated['duree'] = $debut->diffInMinutes($fin) / 60;
        }

        $pointage->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pointage mis à jour avec succès',
            'data' => $pointage->load('matiere')
        ]);
    }

    // Supprimer un pointage (seulement si non validé)
    public function destroy($id)
    {
        $user = auth()->user();
        $enseignant = $user->enseignant;

        $pointage = Pointage::where('enseignant_id', $enseignant->id)
            ->findOrFail($id);

        if ($pointage->statut_pointage === 'approuve') {
            return response()->json([
                'error' => 'Impossible de supprimer un pointage approuvé'
            ], 403);
        }

        $pointage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pointage supprimé avec succès'
        ]);
    }

    // Valider ou refuser (réservé aux administrateurs)
    public function valider(Request $request, $id)
    {
        $pointage = Pointage::findOrFail($id);

        $validated = $request->validate([
            'statut_pointage' => 'required|in:approuve,refuse',
            'motif_rejet' => 'required_if:statut_pointage,refuse|nullable|string'
        ]);

        $pointage->statut_pointage = $validated['statut_pointage'];
        $pointage->motif_rejet = $validated['motif_rejet'] ?? null;
        $pointage->date_validation = now();
        $pointage->validateur_id = auth()->id();
        $pointage->save();

        return response()->json([
            'success' => true,
            'message' => 'Pointage validé avec succès',
            'data' => $pointage
        ]);
    }
}