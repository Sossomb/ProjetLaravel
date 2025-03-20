<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandesTable extends Migration
{
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->timestamp('date_commande');
            $table->enum('statut', ['en_attente', 'en_preparation', 'prete', 'payee']);
            $table->foreignId('client_id')->constrained('users');
            $table->float('montant_total');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commandes');
    }


};
