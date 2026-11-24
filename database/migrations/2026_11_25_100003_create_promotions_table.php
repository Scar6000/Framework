<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('annee_academique_id')->constrained('annees_academiques')->restrictOnDelete();
            $t->foreignId('filiere_id')->constrained('filieres')->restrictOnDelete();
            $t->foreignId('semestre_id')->constrained('semestres')->restrictOnDelete();
            $t->foreignId('specialite_id')->nullable()->constrained('specialites')->restrictOnDelete();
            // Colonne générée : permet l'unicité même quand specialite_id est NULL.
            // (Si votre MySQL/MariaDB refuse cette ligne, supprimez-la : l'unicité est
            //  aussi garantie par PromotionGenerator.)
            $t->unsignedBigInteger('specialite_key')->storedAs('IFNULL(specialite_id, 0)');
            $t->boolean('est_attendue')->default(true);
            $t->date('date_limite')->nullable();
            $t->timestamps();

            $t->unique(['annee_academique_id', 'filiere_id', 'semestre_id', 'specialite_key'], 'promotions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
