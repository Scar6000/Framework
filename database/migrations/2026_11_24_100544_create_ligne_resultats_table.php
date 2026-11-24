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
        Schema::create('ligne_resultats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pv_deliberation_id')->constrained('pv_deliberations')->cascadeOnDelete();
            $table->string('matricule', 50)->index();
            $table->string('nom_prenoms', 180);
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->integer('credits_valides')->default(0);
            $table->enum('decision', ['admis', 'ajourné', 'rattrapage', 'exclu']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_resultats');
    }
};