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
        Schema::create('memoires', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('resume');
            $table->string('mots_cles')->nullable();
            $table->string('fichier_pdf');
            $table->string('statut', 30)->default('soumis');
            $table->text('motif_rejet')->nullable();
            $table->boolean('acces_restreint')->default(false);
            $table->boolean('autoriser_telechargement')->default(false);
            $table->date('embargo_jusqua')->nullable();
            $table->foreignId('etudiant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('encadreur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('filiere_id')->constrained('filieres')->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annee_academiques')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memoires');
    }
};