// Dans le fichier 2025_03_19_135646_correction_enum_statut.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CorrectionEnumStatut extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Pour PostgreSQL, il faut modifier l'énumération différemment
        DB::statement("ALTER TABLE commandes DROP CONSTRAINT IF EXISTS commandes_statut_check");
        DB::statement("ALTER TABLE commandes ADD CONSTRAINT commandes_statut_check
                      CHECK (statut::text = ANY (ARRAY['en_attente'::text, 'en_cours'::text, 'prete'::text, 'livree'::text, 'annulee'::text, 'payee'::text]))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE commandes DROP CONSTRAINT IF EXISTS commandes_statut_check");
        DB::statement("ALTER TABLE commandes ADD CONSTRAINT commandes_statut_check
                      CHECK (statut::text = ANY (ARRAY['en_attente'::text, 'en_preparation'::text, 'prete'::text, 'payee'::text]))");
    }
}
