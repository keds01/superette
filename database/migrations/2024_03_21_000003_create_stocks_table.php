<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->onDelete('restrict');
            
            // Quantités
            $table->integer('quantite_conditionnement')->default(0); // Nombre de cartons/paquets
            $table->integer('quantite_unite')->default(0); // Nombre d'unités individuelles
            $table->integer('quantite_totale')->default(0); // Total calculé en unités
            
            // Seuils d'alerte
            $table->integer('seuil_alerte')->default(0);
            $table->integer('seuil_critique')->default(0);
            
            // Dernière mise à jour
            $table->foreignId('derniere_mise_a_jour_par')->nullable()->constrained('users');
            $table->timestamp('derniere_mise_a_jour_at')->nullable();
            
            // Métadonnées
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour l'historique des mouvements de stock
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            
            // Type de mouvement
            $table->enum('type', ['entree', 'sortie', 'ajustement', 'inventaire']);
            
            // Quantités
            $table->integer('quantite_avant_conditionnement')->default(0);
            $table->integer('quantite_avant_unite')->default(0);
            $table->integer('quantite_apres_conditionnement')->default(0);
            $table->integer('quantite_apres_unite')->default(0);

            // Date de péremption
            $table->date('date_peremption')->nullable();
            
            // Références
            $table->string('reference_mouvement')->nullable(); // Numéro de facture, bon de livraison, etc.
            $table->string('type_reference')->nullable(); // 'facture', 'bon_livraison', 'inventaire', etc.
            
            // Métadonnées
            $table->text('motif')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mouvements_stock');
        Schema::dropIfExists('stocks');
    }
}; 