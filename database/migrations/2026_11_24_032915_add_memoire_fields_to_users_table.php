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
        Schema::table('users', function (Blueprint $table) {
            $table->string('matricule', 30)->nullable()->unique()->after('email');
            $table->string('role', 30)->default('etudiant')->after('matricule');
            $table->foreignId('filiere_id')->nullable()->after('role')->constrained('filieres')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['filiere_id']);
            $table->dropColumn(['matricule', 'role', 'filiere_id']);
        });
    }
};