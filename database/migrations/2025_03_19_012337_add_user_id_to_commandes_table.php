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
        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Exécuter la mise à jour des données après avoir créé la colonne
        DB::statement('UPDATE commandes SET user_id = client_id');
    }


/**
* Reverse the migrations.
*/
public function down(): void
{
Schema::table('commandes', function (Blueprint $table) {
$table->dropForeign(['user_id']);
$table->dropColumn('user_id');
});
}
};
