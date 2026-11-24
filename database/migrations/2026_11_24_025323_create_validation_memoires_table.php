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
        Schema::create('validation_memoires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memoire_id')->constrained('memoires')->cascadeOnDelete();
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ancien_statut', 30)->nullable();
            $table->string('nouveau_statut', 30);
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_memoires');
    }
};