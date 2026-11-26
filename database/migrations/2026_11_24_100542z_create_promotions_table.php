<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->cascadeOnDelete();
            $table->foreignId('filiere_id')->constrained('filieres')->cascadeOnDelete();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->boolean('est_attendue')->default(true);
            $table->date('date_limite')->nullable();
            $table->timestamps();

            $table->unique(['annee_academique_id', 'filiere_id', 'semestre_id'], 'promotions_annee_filiere_semestre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};