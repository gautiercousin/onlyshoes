<?php

/**
 * Consent Helper
 *
 * Fonctions utilitaires pour vérifier les consentements utilisateur (RGPD)
 */

if (!function_exists('has_cookie_consent')) {
    /**
     * Vérifier si l'utilisateur a donné son consentement pour les cookies
     *
     * Pour les utilisateurs connectés: vérifie en base de données
     * Pour les visiteurs anonymes: vérifie le cookie browser
     *
     * @param string $type Type de cookies: 'all' ou 'essential' (pour anonymes uniquement)
     * @return bool TRUE si consentement donné
     */
    function has_cookie_consent(string $type = 'all'): bool
    {
        // Utilisateur connecté: vérifier en DB
        if (session()->get('is_logged_in')) {
            $consentModel = new \App\Models\ConsentModel();
            return $consentModel->hasConsent(session()->get('user_id'), 'cookies');
        }

        // Visiteur anonyme: vérifier cookie browser
        $cookieValue = get_cookie('cookie_consent');

        if (!$cookieValue) {
            return false;
        }

        if ($type === 'all') {
            return $cookieValue === 'all';
        }

        // Pour 'essential', accepte aussi 'all'
        return in_array($cookieValue, ['all', 'essential']);
    }
}

if (!function_exists('has_consent')) {
    /**
     * Vérifier si l'utilisateur a donné un consentement spécifique
     *
     * Requiert que l'utilisateur soit connecté
     *
     * @param string $typeConsentement Type: cookies, conditions_utilisation, traitement_donnees, marketing
     * @return bool TRUE si consentement donné et utilisateur connecté
     */
    function has_consent(string $typeConsentement): bool
    {
        if (!session()->get('is_logged_in')) {
            return false;
        }

        $consentModel = new \App\Models\ConsentModel();
        return $consentModel->hasConsent(session()->get('user_id'), $typeConsentement);
    }
}

if (!function_exists('get_user_consents')) {
    /**
     * Récupérer tous les consentements d'un utilisateur connecté
     *
     * @return array Liste des consentements ou tableau vide si non connecté
     */
    function get_user_consents(): array
    {
        if (!session()->get('is_logged_in')) {
            return [];
        }

        $consentModel = new \App\Models\ConsentModel();
        return $consentModel->getConsentements(session()->get('user_id'));
    }
}

if (!function_exists('can_use_analytics')) {
    /**
     * Vérifier si les cookies d'analyse peuvent être utilisés
     *
     * @return bool TRUE si l'utilisateur a accepté tous les cookies
     */
    function can_use_analytics(): bool
    {
        return has_cookie_consent('all');
    }
}

if (!function_exists('can_use_marketing')) {
    /**
     * Vérifier si les cookies marketing peuvent être utilisés
     *
     * @return bool TRUE si l'utilisateur a accepté tous les cookies
     */
    function can_use_marketing(): bool
    {
        return has_cookie_consent('all');
    }
}
