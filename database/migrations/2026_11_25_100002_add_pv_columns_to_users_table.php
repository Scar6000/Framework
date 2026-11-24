<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('prenoms')->nullable()->after('name');
            $t->string('role', 20)->default('scolarite')->index()->after('password');
            $t->foreignId('filiere_id')->nullable()->after('role')->constrained('filieres')->restrictOnDelete();
            $t->boolean('is_active')->default(true)->after('filiere_id');
            $t->boolean('must_change_password')->default(true)->after('is_active');
            $t->timestamp('last_login_at')->nullable()->after('must_change_password');
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropForeign(['filiere_id']);
            $t->dropColumn(['prenoms', 'role', 'filiere_id', 'is_active', 'must_change_password', 'last_login_at', 'deleted_at']);
        });
    }
};
