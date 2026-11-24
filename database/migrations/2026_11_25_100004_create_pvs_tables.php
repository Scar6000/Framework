<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pvs', function (Blueprint $t) {
            $t->id();
            $t->string('reference')->unique();
            $t->foreignId('promotion_id')->constrained('promotions')->restrictOnDelete();
            $t->string('session', 20)->default('normale');
            $t->date('date_deliberation');
            $t->string('president_jury');
            $t->text('membres_jury')->nullable();
            $t->string('fichier_path');
            $t->string('fichier_nom_original');
            $t->string('fichier_mime', 100);
            $t->unsignedInteger('fichier_taille');
            $t->string('statut', 20)->default('brouillon')->index();
            $t->text('motif_rejet')->nullable();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->timestamp('submitted_at')->nullable();
            $t->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('validated_at')->nullable();
            $t->timestamps();
            $t->softDeletes();

            $t->index(['promotion_id', 'session']);
        });

        Schema::create('etudiants', function (Blueprint $t) {
            $t->id();
            $t->string('matricule', 30)->unique();
            $t->string('nom')->index();
            $t->string('prenoms');
            $t->timestamps();
        });

        Schema::create('lignes_resultats', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pv_id')->constrained('pvs')->cascadeOnDelete();
            $t->foreignId('etudiant_id')->constrained('etudiants')->restrictOnDelete();
            $t->decimal('moyenne', 5, 2);
            $t->unsignedSmallInteger('credits_valides')->default(0);
            $t->string('decision', 20)->index();
            $t->timestamps();

            $t->unique(['pv_id', 'etudiant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_resultats');
        Schema::dropIfExists('etudiants');
        Schema::dropIfExists('pvs');
    }
};
