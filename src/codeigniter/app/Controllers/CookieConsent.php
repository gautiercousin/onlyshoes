<?php

namespace App\Controllers;

use App\Models\ConsentModel;

/**
 * CookieConsent Controller
 *
 * Gère les consentements de cookies (RGPD)
 * - Enregistrement des préférences cookies pour utilisateurs connectés
 * - Migration des consentements anonymes vers compte utilisateur
 */
class CookieConsent extends BaseController
{
    /**
     * Enregistrer le consentement cookies (AJAX)
     *
     * Appelé par JavaScript quand l'utilisateur clique sur un bouton de la bannière
     * Enregistre le consentement en base de données pour les utilisateurs connectés
     *
     * URL: POST /cookies/save
     * Body JSON: { "accept_all": true|false }
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function save()
    {
        // Vérifier que c'est une requête AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Requête invalide'
            ]);
        }

        // Vérifier que l'utilisateur est connecté
        if (!session()->get('is_logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Non authentifié'
            ]);
        }

        // Récupérer les données
        $json = $this->request->getJSON();
        $acceptAll = $json->accept_all ?? false;

        $userId = session()->get('user_id');
        $consentModel = new ConsentModel();

        // Vérifier si un consentement existe déjà
        $existingConsent = $consentModel->hasConsent($userId, 'cookies');

        if ($existingConsent) {
            // Consentement déjà donné, ne rien faire
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Consentement déjà enregistré'
            ]);
        }

        // Créer le consentement
        $consent = $consentModel->creerConsentement($userId, 'cookies', true);

        if ($consent) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Consentement enregistré',
                'type' => $acceptAll ? 'all' : 'essential'
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Erreur lors de l\'enregistrement'
            ]);
        }
    }

    /**
     * Retirer le consentement cookies
     *
     * Permet à un utilisateur de retirer son consentement
     *
     * URL: POST /cookies/withdraw
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function withdraw()
    {
        // Vérifier que l'utilisateur est connecté
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté');
            return redirect()->to(base_url('/'));
        }

        $userId = session()->get('user_id');
        $consentModel = new ConsentModel();

        // Récupérer tous les consentements de type 'cookies'
        $consents = $consentModel->getConsentements($userId);

        foreach ($consents as $consent) {
            if ($consent['type_consentement'] === 'cookies' && $consent['statut'] === true) {
                $consentModel->retirerConsentement($consent['id_consentement']);
            }
        }

        session()->setFlashdata('success', 'Votre consentement a été retiré. La bannière réapparaîtra à votre prochaine visite.');
        return redirect()->back();
    }

    /**
     * Page de gestion des préférences cookies
     *
     * URL: GET /cookies/preferences
     *
     * @return string Vue de gestion des préférences
     */
    public function preferences()
    {
        $data = [
            'title' => 'Préférences cookies'
        ];

        // Si utilisateur connecté, récupérer ses consentements
        if (session()->get('is_logged_in')) {
            $consentModel = new ConsentModel();
            $userId = session()->get('user_id');
            $data['consents'] = $consentModel->getConsentements($userId);
        } else {
            $data['consents'] = [];
        }

        return view('CookieConsent/preferences', $data);
    }
}
