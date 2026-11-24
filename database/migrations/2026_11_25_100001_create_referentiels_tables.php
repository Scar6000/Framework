<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filieres', function (Blueprint $t) {
            $t->id();
            $t->string('code', 10)->unique();
            $t->string('nom');
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('niveaux', function (Blueprint $t) {
            $t->id();
            $t->string('code', 5)->unique();
            $t->string('libelle');
            $t->unsignedTinyInteger('ordre');
            $t->timestamps();
        });

        Schema::create('annees_academiques', function (Blueprint $t) {
            $t->id();
            $t->string('libelle', 9)->unique();
            $t->date('date_debut');
            $t->date('date_fin');
            $t->boolean('est_en_cours')->default(false);
            $t->timestamps();
        });

        Schema::create('specialites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('filiere_id')->constrained('filieres')->restrictOnDelete();
            $t->string('code', 10);
            $t->string('nom');
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['filiere_id', 'code']);
        });

        Schema::create('semestres', function (Blueprint $t) {
            $t->id();
            $t->string('code', 5)->unique();
            $t->string('libelle');
            $t->foreignId('niveau_id')->constrained('niveaux')->restrictOnDelete();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semestres');
        Schema::dropIfExists('specialites');
        Schema::dropIfExists('annees_academiques');
        Schema::dropIfExists('niveaux');
        Schema::dropIfExists('filieres');
    }
};
