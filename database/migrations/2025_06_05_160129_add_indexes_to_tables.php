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
        // Index pour les organisations
        Schema::table('organizations', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Index pour le matériel
        Schema::table('equipments', function (Blueprint $table) {
            $table->index('organization_id');
            $table->index('category');
            $table->index('is_available');
            $table->index('is_rentable');
        });

        // Index pour les réservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->index('organization_id');
            $table->index('equipment_id');
            $table->index('user_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });

        // Index pour la table pivot organization_user
        Schema::table('organization_user', function (Blueprint $table) {
            $table->index(['organization_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les index des organisations
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        // Supprimer les index du matériel
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropIndex(['category']);
            $table->dropIndex(['is_available']);
            $table->dropIndex(['is_rentable']);
        });

        // Supprimer les index des réservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropIndex(['equipment_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date', 'end_date']);
        });

        // Supprimer les index de la table pivot
        Schema::table('organization_user', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'user_id']);
        });
    }
};
