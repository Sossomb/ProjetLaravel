<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateCommandesStatusConstraint extends Migration
{
    public function up()
    {
        // Pour PostgreSQL, nous devons d'abord supprimer la contrainte enum existante
        DB::statement("ALTER TABLE commandes DROP CONSTRAINT IF EXISTS commandes_statut_check");

        // Puis ajouter une nouvelle contrainte avec toutes les valeurs
        DB::statement("ALTER TABLE commandes ADD CONSTRAINT commandes_statut_check CHECK (statut IN ('en_attente', 'en_cours', 'en_preparation', 'prete', 'livree', 'payee', 'annulee'))");
    }

    public function down()
    {
        // Revenir à la contrainte originale en cas de rollback
        DB::statement("ALTER TABLE commandes DROP CONSTRAINT IF EXISTS commandes_statut_check");
        DB::statement("ALTER TABLE commandes ADD CONSTRAINT commandes_statut_check CHECK (statut IN ('en_attente', 'en_preparation', 'prete', 'payee'))");
    }
}
