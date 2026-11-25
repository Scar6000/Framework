<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liens_partage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pv_id')->constrained('pvs')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->unsignedInteger('max_views')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liens_partage');
    }
};