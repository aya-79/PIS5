<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();

            $table->date('date');                          // Date de la séance
            $table->time('heure_debut');                   // Heure de début
            $table->time('heure_fin');                     // Heure de fin
            $table->float('duree');
            $table->enum('type_seance', ['CM', 'TD', 'TP']);
            
            // Statut du pointage
            $table->enum('statut_pointage', ['EN_ATTENTE', 'APPROUVE', 'REJETE'])
                ->default('EN_ATTENTE');
            
            $table->string('motif_rejet', 500)->nullable(); 
            
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_validation')->nullable();

            // 1. Relation avec enseignants
            $table->foreignId('enseignant_id')
                ->constrained('enseignants')
                ->onDelete('cascade');
            
            // 2. Relation avec matieres
            $table->foreignId('matiere_id')
                ->constrained('matieres')
                ->onDelete('cascade');
            
            // 3. Relation avec annee_scolaires
            $table->foreignId('annee_scolaire_id')
                ->constrained('annee_scolaires')
                ->onDelete('cascade');

            // 4. Relation avec agent_scolarites (validateur)
            $table->foreignId('validateur_id')
              ->nullable()
              ->constrained('agent_scolarites')
              ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
