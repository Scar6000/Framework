<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lignes_resultats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pv_id')->constrained('pvs')->cascadeOnDelete();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->decimal('moyenne_generale', 4, 2);
            $table->enum('decision', ['admis', 'ajourne', 'rattrapage', 'exclu', 'absent']);
            $table->string('mention')->nullable();
            $table->unsignedInteger('rang')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->unique(['pv_id', 'etudiant_id'], 'lignes_resultats_pv_etudiant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_resultats');
    }
};