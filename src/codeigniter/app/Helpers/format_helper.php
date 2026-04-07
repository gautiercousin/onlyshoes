<?php

/**
 * Helper pour formater les données de la base de données
 */

if (!function_exists('format_etat')) {
    /**
     * Formate l'état d'une annonce pour l'affichage
     *
     * Convertit 'comme_neuf' en 'Comme Neuf', 'bon' en 'Bon', etc.
     *
     * @param string $etat État depuis la base de données
     * @return string État formaté pour affichage
     */
    function format_etat(string $etat): string
    {
        // Remplacer les underscores par des espaces
        $etat = str_replace('_', ' ', $etat);

        // Mettre la première lettre de chaque mot en majuscule
        return ucwords($etat);
    }
}
