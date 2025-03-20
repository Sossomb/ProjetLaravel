<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // D'abord, nous enlevons les contraintes existantes sur la colonne role
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->change();
        });

        // Ensuite, nous convertissons la colonne en type enum avec seulement "gestionnaire" et "client"
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255)");
        DB::statement("ALTER TABLE users ADD CONSTRAINT check_role CHECK (role IN ('client', 'gestionnaire'))");

        // Enfin, nous définissons la valeur par défaut
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'client'");

        // Nous nous assurons que tous les enregistrements existants sans rôle sont définis comme 'client'
        DB::statement("UPDATE users SET role = 'client' WHERE role IS NULL");

        // Nous rendons la colonne obligatoire
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la contrainte de vérification
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS check_role");

        // Remettre la colonne comme elle était avant
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->change();
        });
    }
};
