<?php
/**
 * Bannière de consentement cookies (GDPR)
 *
 * Affichée uniquement si l'utilisateur n'a pas encore donné son consentement
 * - Pour utilisateurs connectés: vérifie CONSENTEMENT_UTILISATEUR (type='cookies')
 * - Pour visiteurs anonymes: vérifie cookie browser 'cookie_consent'
 *
 * Deux options:
 * - "Accepter tous les cookies" → Active tous les types (essentiels, analytics, marketing, personnalisation)
 * - "Cookies essentiels uniquement" → Active uniquement les cookies nécessaires
 */

// Vérifier si l'utilisateur a déjà donné son consentement
$hasConsent = false;

if (session()->get('is_logged_in')) {
    // Utilisateur connecté: vérifier en base de données
    $consentModel = new \App\Models\ConsentModel();
    $hasConsent = $consentModel->hasConsent(session()->get('user_id'), 'cookies');
}
// Pour les anonymes, le JavaScript vérifiera le cookie browser

// Ne pas afficher si consentement déjà donné
if ($hasConsent) {
    return;
}
?>

<!-- Bannière cookies (RGPD) -->
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 shadow-2xl z-50" style="display: none;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Message -->
            <div class="flex-1 text-sm text-gray-700">
                <p class="font-semibold mb-2">Nous utilisons des cookies</p>
                <p>
                    Nous utilisons des cookies pour améliorer votre expérience, analyser le trafic et personnaliser le contenu.
                    En cliquant sur "Accepter tous les cookies", vous acceptez notre utilisation des cookies.
                    <a href="/confidentialite" class="text-green-600 hover:underline font-medium">En savoir plus</a>
                </p>
            </div>

            <!-- Boutons -->
            <div class="flex flex-col sm:flex-row gap-3 min-w-fit">
                <button
                    id="cookie-essential-only"
                    class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition whitespace-nowrap">
                    Essentiels uniquement
                </button>
                <button
                    id="cookie-accept-all"
                    class="px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition whitespace-nowrap">
                    Accepter tous les cookies
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    // Configuration
    const COOKIE_NAME = 'cookie_consent';
    const COOKIE_EXPIRY_DAYS = 365;

    // Récupérer les éléments
    const banner = document.getElementById('cookie-banner');
    const acceptAllBtn = document.getElementById('cookie-accept-all');
    const essentialOnlyBtn = document.getElementById('cookie-essential-only');

    /**
     * Vérifier si un cookie existe
     */
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    /**
     * Définir un cookie
     */
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    }

    /**
     * Envoyer le consentement au serveur (pour utilisateurs connectés uniquement)
     */
    function sendConsentToServer(acceptAll) {
        // Vérifier si l'utilisateur est connecté (présence de session)
        <?php if (session()->get('is_logged_in')): ?>
        fetch('/cookies/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                accept_all: acceptAll
            })
        }).catch(err => {
            console.error('Erreur enregistrement consentement:', err);
        });
        <?php endif; ?>
    }

    /**
     * Gérer l'acceptation des cookies
     */
    function handleConsent(acceptAll) {
        const consentValue = acceptAll ? 'all' : 'essential';

        console.log('Cookie consent given:', consentValue);

        // Enregistrer le choix dans un cookie browser
        setCookie(COOKIE_NAME, consentValue, COOKIE_EXPIRY_DAYS);

        // Envoyer au serveur si connecté
        sendConsentToServer(acceptAll);

        // Masquer la bannière
        banner.style.display = 'none';

        // Activer les cookies selon le choix
        if (acceptAll) {
            enableAllCookies();
        } else {
            enableEssentialCookies();
        }
    }

    /**
     * Activer tous les cookies (analytics, marketing, etc.)
     */
    function enableAllCookies() {
        // TODO: Activer Google Analytics, Facebook Pixel, etc.
        console.log('Tous les cookies activés');
    }

    /**
     * Activer uniquement les cookies essentiels
     */
    function enableEssentialCookies() {
        // Les cookies essentiels sont déjà actifs (session, CSRF, etc.)
        console.log('Cookies essentiels uniquement');
    }

    /**
     * Vérifier si le consentement existe déjà
     */
    function checkExistingConsent() {
        <?php if (session()->get('is_logged_in')): ?>
            // Pour les utilisateurs connectés, PHP a déjà vérifié en DB
            // Si on arrive ici, c'est qu'il n'y a pas de consentement en DB
            // Vérifier quand même le cookie browser (cas où l'utilisateur a consenti avant de se connecter)
            const consent = getCookie(COOKIE_NAME);
            if (consent) {
                // Migrer le consentement du cookie vers la DB
                sendConsentToServer(consent === 'all');
                return true;
            }
            return false;
        <?php else: ?>
            // Pour les visiteurs anonymes, vérifier uniquement le cookie
            const consent = getCookie(COOKIE_NAME);
            if (consent) {
                // Consentement déjà donné, activer les cookies correspondants
                if (consent === 'all') {
                    enableAllCookies();
                } else {
                    enableEssentialCookies();
                }
                return true;
            }
            return false;
        <?php endif; ?>
    }

    /**
     * Initialiser la bannière
     */
    function init() {
        // Vérifier si consentement existe
        const hasConsent = checkExistingConsent();

        console.log('Cookie consent check:', {
            hasConsent: hasConsent,
            cookieValue: getCookie(COOKIE_NAME),
            isLoggedIn: <?= session()->get('is_logged_in') ? 'true' : 'false' ?>
        });

        // Afficher la bannière si pas de consentement
        if (!hasConsent) {
            banner.style.display = 'block';
            console.log('Cookie banner displayed');
        } else {
            console.log('Cookie banner hidden (consent already given)');
        }

        // Event listeners
        acceptAllBtn.addEventListener('click', () => handleConsent(true));
        essentialOnlyBtn.addEventListener('click', () => handleConsent(false));
    }

    // Initialiser au chargement de la page
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
