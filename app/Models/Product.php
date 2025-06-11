<?php

namespace App\Models;

/**
 * Alias léger pour compatibilité : certaines parties du code utilisaient
 * encore le nom `Product` au lieu de `Produit`. Cette classe étend donc
 * simplement `Produit` afin d'éviter d'avoir à refactorer partout.
 * Elle pointe sur la même table et hérite de toutes les relations,
 * casts, attributs et méthodes sans duplication de logique.
 */
class Product extends Produit
{
    // Aucune surcharge nécessaire. Tout est hérité du modèle Produit.
}
