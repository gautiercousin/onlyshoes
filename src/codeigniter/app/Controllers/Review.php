<?php

namespace App\Controllers;

use App\Models\ReviewModel;
use CodeIgniter\HTTP\RedirectResponse;

class Review extends BaseController
{
    protected $reviewModel;

    /**
     * Constructeur - Initialise le modèle Review
     */
    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Afficher le formulaire de création d'un avis
     *
     * Permet à un acheteur de laisser un avis sur un vendeur après livraison
     * Vérifie que l'utilisateur est connecté et a bien acheté chez ce vendeur
     *
     * URL: /review/creer/{id_vendeur}
     * Paramètres GET: redirect (optionnel) - URL de redirection après soumission
     *
     * @param string $idVendeur UUID du vendeur à évaluer
     * @return string|RedirectResponse Vue du formulaire ou redirection si erreur
     */
    public function creer(string $idVendeur): string|RedirectResponse
    {
        // Vérifier l'authentification
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté pour laisser un avis.');
            return redirect()->to(base_url('connexion'));
        }

        // Bloquer les admins
        if (session()->get('user_type_compte') === 'admin') {
            session()->setFlashdata('error', 'Les administrateurs ne peuvent pas laisser d\'avis.');
            return redirect()->to(base_url('admin/dashboard'));
        }

        $userId = session()->get('user_id');

        // Vérifier qu'un avis n'existe pas déjà
        $avisExistant = $this->reviewModel->getAvisExistant($userId, $idVendeur);
        if ($avisExistant) {
            session()->setFlashdata('error', 'Vous avez déjà laissé un avis pour ce vendeur.');
            return redirect()->to(base_url('commandes'));
        }

        // Récupérer les informations du vendeur
        $db = \Config\Database::connect();
        $vendeurQuery = $db->query("SELECT * FROM utilisateur_read(?::uuid)", [$idVendeur]);
        $vendeur = $vendeurQuery->getRowArray();

        if (!$vendeur) {
            session()->setFlashdata('error', 'Vendeur introuvable.');
            return redirect()->to(base_url('commandes'));
        }

        $data = [
            'title' => 'Laisser un avis',
            'vendeur' => $vendeur,
            'redirect' => $this->request->getGet('redirect') ?? base_url('commandes')
        ];

        return view('Review/creer', $data);
    }

    /**
     * Traiter la soumission du formulaire de création d'avis
     *
     * Valide les données (note 1-5, commentaire optionnel)
     * Crée l'avis via review_create() qui vérifie les règles métier (SA023, SA024)
     *
     * URL: POST /review/store
     * Paramètres POST: id_vendeur, note, commentaire, redirect
     *
     * @return RedirectResponse Redirection avec message flash
     */
    public function store(): RedirectResponse
    {
        // Vérifier l'authentification
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté.');
            return redirect()->to(base_url('connexion'));
        }

        if (session()->get('user_type_compte') === 'admin') {
            session()->setFlashdata('error', 'Accès non autorisé.');
            return redirect()->to(base_url('admin/dashboard'));
        }

        $userId = session()->get('user_id');
        $idVendeur = $this->request->getPost('id_vendeur');
        $note = (int) $this->request->getPost('note');
        $commentaire = $this->request->getPost('commentaire');
        $redirect = $this->request->getPost('redirect') ?? base_url('commandes');

        // Validation
        if (empty($idVendeur)) {
            session()->setFlashdata('error', 'Vendeur non spécifié.');
            return redirect()->to(base_url('commandes'));
        }

        if ($note < 1 || $note > 5) {
            session()->setFlashdata('error', 'La note doit être entre 1 et 5.');
            return redirect()->to(base_url('review/creer/' . $idVendeur));
        }

        // Créer l'avis via le modèle
        $avisData = [
            'note' => $note,
            'commentaire' => $commentaire ?? ''
        ];

        $avis = $this->reviewModel->creerAvis($userId, $idVendeur, $avisData);

        if (!$avis) {
            // Gérer les erreurs de la procédure stockée (SA023, SA024)
            $db = \Config\Database::connect();
            $error = $db->error();

            if (isset($error['code'])) {
                if ($error['code'] === 'SA023') {
                    session()->setFlashdata('error', 'Vous avez déjà laissé un avis pour ce vendeur.');
                } elseif ($error['code'] === 'SA024') {
                    session()->setFlashdata('error', 'Vous devez avoir acheté au moins un produit de ce vendeur pour laisser un avis.');
                } else {
                    session()->setFlashdata('error', 'Erreur lors de la création de l\'avis.');
                }
            } else {
                session()->setFlashdata('error', 'Erreur lors de la création de l\'avis.');
            }

            return redirect()->to(base_url('review/creer/' . $idVendeur));
        }

        session()->setFlashdata('success', 'Votre avis a été publié avec succès. Merci pour votre retour!');
        return redirect()->to($redirect);
    }

    /**
     * Afficher le formulaire de modification d'un avis
     *
     * Seul l'auteur de l'avis peut le modifier
     *
     * URL: /review/modifier/{id}
     * Paramètres GET: redirect (optionnel)
     *
     * @param string $idReview UUID de l'avis
     * @return string|RedirectResponse Vue du formulaire ou redirection si erreur
     */
    public function modifier(string $idReview): string|RedirectResponse
    {
        // Vérifier l'authentification
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté.');
            return redirect()->to(base_url('connexion'));
        }

        // Bloquer les admins
        if (session()->get('user_type_compte') === 'admin') {
            session()->setFlashdata('error', 'Les administrateurs ne peuvent pas modifier d\'avis.');
            return redirect()->to(base_url('admin/dashboard'));
        }

        $userId = session()->get('user_id');

        // Récupérer l'avis
        $avis = $this->reviewModel->getAvis($idReview);

        if (!$avis) {
            session()->setFlashdata('error', 'Avis introuvable.');
            return redirect()->to(base_url('commandes'));
        }

        // Vérifier que l'utilisateur est l'auteur
        if ($avis['id_utilisateur_auteur'] !== $userId) {
            session()->setFlashdata('error', 'Vous ne pouvez pas modifier cet avis.');
            return redirect()->to(base_url('commandes'));
        }

        // Récupérer les informations du vendeur
        $db = \Config\Database::connect();
        $vendeurQuery = $db->query("SELECT * FROM utilisateur_read(?::uuid)", [$avis['id_utilisateur_vendeur']]);
        $vendeur = $vendeurQuery->getRowArray();

        $data = [
            'title' => 'Modifier mon avis',
            'avis' => $avis,
            'vendeur' => $vendeur,
            'redirect' => $this->request->getGet('redirect') ?? base_url('commandes')
        ];

        return view('Review/modifier', $data);
    }

    /**
     * Traiter la soumission du formulaire de modification
     *
     * URL: POST /review/update/{id}
     * Paramètres POST: note, commentaire, redirect
     *
     * @param string $idReview UUID de l'avis
     * @return RedirectResponse Redirection avec message flash
     */
    public function update(string $idReview): RedirectResponse
    {
        // Vérifier l'authentification
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté.');
            return redirect()->to(base_url('connexion'));
        }

        // Bloquer les admins
        if (session()->get('user_type_compte') === 'admin') {
            session()->setFlashdata('error', 'Les administrateurs ne peuvent pas modifier d\'avis.');
            return redirect()->to(base_url('admin/dashboard'));
        }

        $userId = session()->get('user_id');

        // Récupérer l'avis
        $avis = $this->reviewModel->getAvis($idReview);

        if (!$avis || $avis['id_utilisateur_auteur'] !== $userId) {
            session()->setFlashdata('error', 'Vous ne pouvez pas modifier cet avis.');
            return redirect()->to(base_url('commandes'));
        }

        $note = (int) $this->request->getPost('note');
        $commentaire = $this->request->getPost('commentaire');
        $redirect = $this->request->getPost('redirect') ?? base_url('commandes');

        // Validation
        if ($note < 1 || $note > 5) {
            session()->setFlashdata('error', 'La note doit être entre 1 et 5.');
            return redirect()->to(base_url('review/modifier/' . $idReview));
        }

        // Modifier l'avis
        $avisData = [
            'note' => $note,
            'commentaire' => $commentaire ?? ''
        ];

        $updated = $this->reviewModel->modifierAvis($idReview, $avisData);

        if (!$updated) {
            session()->setFlashdata('error', 'Erreur lors de la modification de l\'avis.');
            return redirect()->to(base_url('review/modifier/' . $idReview));
        }

        session()->setFlashdata('success', 'Votre avis a été modifié avec succès.');
        return redirect()->to($redirect);
    }

    /**
     * Supprimer un avis
     *
     * Seul l'auteur peut supprimer son avis
     *
     * URL: POST /review/supprimer/{id}
     * Paramètres POST: redirect (optionnel)
     *
     * @param string $idReview UUID de l'avis
     * @return RedirectResponse Redirection avec message flash
     */
    public function supprimer(string $idReview): RedirectResponse
    {
        // Vérifier l'authentification
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté.');
            return redirect()->to(base_url('connexion'));
        }

        // Bloquer les admins
        if (session()->get('user_type_compte') === 'admin') {
            session()->setFlashdata('error', 'Les administrateurs ne peuvent pas supprimer d\'avis.');
            return redirect()->to(base_url('admin/dashboard'));
        }

        $userId = session()->get('user_id');

        // Récupérer l'avis
        $avis = $this->reviewModel->getAvis($idReview);

        if (!$avis) {
            session()->setFlashdata('error', 'Avis introuvable.');
            return redirect()->to(base_url('commandes'));
        }

        // Vérifier que l'utilisateur est l'auteur
        if ($avis['id_utilisateur_auteur'] !== $userId) {
            session()->setFlashdata('error', 'Vous ne pouvez pas supprimer cet avis.');
            return redirect()->to(base_url('commandes'));
        }

        // Supprimer l'avis
        $success = $this->reviewModel->supprimerAvis($idReview);

        if (!$success) {
            session()->setFlashdata('error', 'Erreur lors de la suppression de l\'avis.');
        } else {
            session()->setFlashdata('success', 'Votre avis a été supprimé avec succès.');
        }

        $redirect = $this->request->getPost('redirect') ?? base_url('commandes');
        return redirect()->to($redirect);
    }
}
