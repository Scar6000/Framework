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
        Schema::create('pv_deliberations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_academique_id')->constrained('annee_academiques')->cascadeOnDelete();
            $table->foreignId('filiere_id')->constrained('filieres')->cascadeOnDelete();
            $table->foreignId('niveau_id')->constrained('niveaux')->cascadeOnDelete();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->foreignId('createur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference', 100)->unique();
            $table->enum('session', ['normale', 'rattrapage']);
            $table->date('date_deliberation');
            $table->string('president_jury', 150);
            $table->text('membres_jury')->nullable();
            $table->string('fichier_scan', 255);
            $table->enum('statut', ['brouillon', 'valide', 'archive'])->default('brouillon');
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pv_deliberations');
    }
};