<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDelaiAlertePeremptionToProduits extends Migration
{
    /**
     * Ajoute un champ pour personnaliser le délai d'alerte de péremption par produit.
     * La valeur par défaut est NULL, ce qui signifie utiliser le délai global de 30 jours.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->integer('delai_alerte_peremption')->nullable()->after('date_peremption')
                  ->comment('Délai en jours avant péremption pour déclencher une alerte (NULL = utiliser valeur par défaut 30 jours)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn('delai_alerte_peremption');
        });
    }
}
