<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pvs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->enum('session', ['normale', 'rattrapage']);
            $table->date('date_deliberation');
            $table->string('president_jury');
            $table->json('membres_jury')->nullable();
            $table->string('fichier_path');
            $table->string('fichier_nom_original');
            $table->string('fichier_mime');
            $table->unsignedInteger('fichier_taille');
            $table->enum('statut', ['brouillon', 'soumis', 'valide', 'archive'])->default('brouillon');
            $table->text('motif_rejet')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['promotion_id', 'session'], 'pvs_promotion_session_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pvs');
    }
};