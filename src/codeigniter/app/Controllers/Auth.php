<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;

class Auth extends BaseController
{
    /**
     * Afficher la page de connexion
     *
     * Si l'utilisateur est déjà connecté, redirige vers l'accueil
     *
     * URL: /connexion
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse Vue de connexion ou redirection
     */
    public function connexion(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('/'));
        }

        return view('Auth/connexion');
    }

    /**
     * Afficher la page d'inscription
     *
     * Si l'utilisateur est déjà connecté, redirige vers l'accueil
     *
     * URL: /inscription
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse Vue d'inscription ou redirection
     */
    public function inscription(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('/'));
        }

        return view('Auth/inscription');
    }

    /**
     * Traiter la soumission du formulaire de connexion
     *
     * Valide les credentials via utilisateur_login() et crée une session
     * Vérifie le statut du compte (actif/banni/suspendu)
     *
     * URL: POST /connexion
     * Paramètres POST: email, password
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection avec message flash
     */
    public function doConnexion()
    {
        // Récupérer les données du formulaire
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validation basique
        if (empty($email) || empty($password)) {
            session()->setFlashdata('error', 'Veuillez remplir tous les champs');
            return redirect()->to(base_url('connexion'));
        }

        // Appeler le Model pour vérifier les credentials
        $utilisateurModel = new UtilisateurModel();
        $user = $utilisateurModel->seConnecter($email, $password);

        // Si credentials invalides
        if (!$user) {
            session()->setFlashdata('error', 'Email ou mot de passe incorrect');
            return redirect()->to(base_url('connexion'));
        }

        // Vérifier si le compte est banni ou suspendu
        if (isset($user['error'])) {
            if ($user['error'] === 'account_banned') {
                session()->setFlashdata('error', 'Votre compte a été banni définitivement. Vous ne pouvez plus accéder à la plateforme. Contactez l\'administrateur pour plus d\'informations.');
                return redirect()->to(base_url('connexion'));
            }
            if ($user['error'] === 'account_suspended') {
                session()->setFlashdata('error', 'Votre compte a été suspendu temporairement. Vous ne pouvez pas vous connecter pour le moment. Contactez l\'administrateur pour plus d\'informations.');
                return redirect()->to(base_url('connexion'));
            }
        }

        // Credentials valides! Créer la session
        session()->set([
            'user_id' => $user['id_utilisateur'],
            'user_email' => $user['email'],
            'user_nom' => $user['nom'],
            'user_prenom' => $user['prenom'],
            'user_type_compte' => $user['type_compte'],
            'is_logged_in' => true
        ]);

        // Message de succès
        session()->setFlashdata('success', 'Connexion réussie! Bienvenue ' . $user['prenom']);

        // Rediriger vers l'accueil
        return redirect()->to(base_url('/'));
    }

    /**
     * Traiter la soumission du formulaire d'inscription
     *
     * Valide les données (champs requis, concordance mots de passe, longueur minimale)
     * Crée le compte via utilisateur_create() avec mot de passe bcrypt
     * Crée automatiquement une session après l'inscription réussie
     *
     * URL: POST /inscription
     * Paramètres POST: prenom, nom, email, password, password_confirm
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection avec message flash
     */
    public function doInscription()
    {
        // Récupérer les données du formulaire
        $prenom = $this->request->getPost('prenom');
        $nom = $this->request->getPost('nom');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        $terms = $this->request->getPost('terms');

        // Validation basique
        if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
            session()->setFlashdata('error', 'Veuillez remplir tous les champs');
            return redirect()->to(base_url('inscription'));
        }

        // Vérifier que les conditions sont acceptées (GDPR requis)
        if (!$terms) {
            session()->setFlashdata('error', 'Vous devez accepter les conditions générales et la politique de confidentialité');
            return redirect()->to(base_url('inscription'));
        }

        // Vérifier que les mots de passe correspondent
        if ($password !== $passwordConfirm) {
            session()->setFlashdata('error', 'Les mots de passe ne correspondent pas');
            return redirect()->to(base_url('inscription'));
        }

        // Vérifier longueur minimale du mot de passe
        if (strlen($password) < 8) {
            session()->setFlashdata('error', 'Le mot de passe doit contenir au moins 8 caractères');
            return redirect()->to(base_url('inscription'));
        }

        // Préparer les données pour le Model (noms du diagramme de classes)
        $userData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $password  // Sera haché dans le Model
        ];

        // Appeler le Model pour créer le compte
        $utilisateurModel = new UtilisateurModel();
        $user = $utilisateurModel->creerCompte($userData);

        // Si échec (email déjà utilisé)
        if (!$user) {
            session()->setFlashdata('error', 'Cet email est déjà utilisé');
            return redirect()->to(base_url('inscription'));
        }

        // Créer les consentements obligatoires (CGV + GDPR)
        $consentModel = new \App\Models\ConsentModel();
        $consentsCreated = $consentModel->creerConsentsInscription($user['id_utilisateur']);

        if (!$consentsCreated) {
            log_message('warning', 'Échec création consentements pour utilisateur: ' . $user['id_utilisateur']);
        }

        // Succès! Créer la session automatiquement
        session()->set([
            'user_id' => $user['id_utilisateur'],
            'user_email' => $user['email'],
            'user_nom' => $user['nom'],
            'user_prenom' => $user['prenom'],
            'user_type_compte' => $user['type_compte'],
            'is_logged_in' => true
        ]);

        // Message de succès
        session()->setFlashdata('success', 'Compte créé avec succès! Bienvenue ' . $user['prenom']);

        // Rediriger vers l'accueil
        return redirect()->to(base_url('/'));
    }

    /**
     * Déconnecter l'utilisateur
     *
     * Détruit la session et redirige vers l'accueil
     *
     * URL: GET /deconnexion
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers l'accueil
     */
    public function deconnexion()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}
