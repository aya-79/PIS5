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
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();

            $table->string('code', 20)->unique();  
            $table->string('nom', 255);            
            $table->text('description')->nullable(); 
            $table->enum('niveau', ['L1', 'L2','L3','M1','M2']); 
            $table->integer('credit');             
            $table->string('filiere', 100);        
            $table->enum('semestre',['S1','S2','S3','S4','S5','S6']); 
            $table->float('nombre_heures_prevu');  

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};
