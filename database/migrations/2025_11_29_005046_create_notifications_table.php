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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

             // Type de notification
            $table->enum('type', [
                'POINTAGE_SOUMIS',
                'POINTAGE_APPROUVE', 
                'POINTAGE_REJETE'
            ]);
            
            $table->text('message');                       // Contenu de la notification
            $table->timestamp('date_envoi')->useCurrent(); 

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->foreignId('pointage_id')
                ->nullable()
                ->constrained('pointages')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
