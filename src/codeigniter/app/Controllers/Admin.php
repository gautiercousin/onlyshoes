<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Models\AdminModel;
use App\Models\SignalementModel;
use App\Models\MarqueModel;
use App\Models\CouleurModel;
use App\Models\MateriauModel;

class Admin extends BaseController
{
    private $adminModel;
    private $signalementModel;

    /**
     * Constructeur - Initialise les modèles Admin et Signalement
     */
    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->signalementModel = new SignalementModel();
    }

    /**
     * Middleware - Vérifier si l'utilisateur est admin
     *
     * Vérifie l'authentification et le type de compte
     * Redirige vers /connexion si non autorisé
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|null Redirection si non autorisé, null sinon
     */
    private function checkAdminAuth()
    {
        if (!session()->get('is_logged_in') || session()->get('user_type_compte') !== 'admin') {
            session()->setFlashdata('error', 'Accès non autorisé. Veuillez vous connecter en tant qu\'administrateur.');
            return redirect()->to(base_url('connexion'));
        }
        return null;
    }

    /**
     * Afficher le tableau de bord administrateur
     *
     * Affiche les statistiques principales :
     * - Nombre total de comptes utilisateurs
     * - Nombre total de produits (annonces)
     * - Nombre de signalements en attente
     *
     * URL: /admin/dashboard
     *
     * @return string Vue du dashboard admin
     */
    public function dashboard()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        try {
            // Récupérer les statistiques
            $data = [
                'total_comptes' => $this->adminModel->getTotalComptes(),
                'total_produits' => $this->adminModel->getTotalProduits(),
                'signalements_en_attente' => $this->adminModel->getSignalementsEnAttente(),
                'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
            ];
        } catch (\Exception $e) {
            log_message('error', 'Erreur dashboard admin: ' . $e->getMessage());
            // Valeurs par défaut en cas d'erreur
            $data = [
                'total_comptes' => 0,
                'total_produits' => 0,
                'signalements_en_attente' => 0,
                'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
            ];
        }

        return view('Admin/dashboard', $data);
    }

    /**
     * Déconnecter l'administrateur
     *
     * Détruit la session et redirige vers la page de connexion
     *
     * URL: POST /admin/logout
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers /connexion
     */
    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();
        session()->setFlashdata('success', 'Vous avez été déconnecté avec succès');
        return redirect()->to(base_url('connexion'));
    }

    /**
     * Point d'entrée /admin
     *
     * Redirige vers /admin/dashboard si admin connecté, sinon vers /connexion
     *
     * URL: /admin
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers dashboard ou connexion
     */
    public function index(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('is_logged_in') && session()->get('user_type_compte') === 'admin') {
            return redirect()->to(base_url('admin/dashboard'));
        }
        return redirect()->to(base_url('connexion'));
    }

    /**
     * Afficher la page de gestion des signalements
     *
     * Liste tous les signalements (annonces, reviews, comptes) avec filtres :
     * - Par statut : en_attente, traite, rejete, tous
     * - Par type : annonce, user, review, tous
     *
     * URL: /admin/signalements
     * Paramètres GET: statut (défaut: en_attente), type (défaut: tous)
     *
     * @return string Vue de gestion des signalements
     */
    public function signalements()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        // Récupérer les paramètres de filtrage
        $statut = $this->request->getGet('statut') ?? 'en_attente';
        $type = $this->request->getGet('type') ?? 'tous';
        $page = (int) ($this->request->getGet('page') ?? 1);

        // Récupérer les signalements filtrés avec pagination (20 par page)
        $result = $this->adminModel->getAllSignalements($statut, $type, $page, 20);

        $data = [
            'signalements' => $result['signalements'],
            'pagination' => $result['pagination'],
            'statut' => $statut,
            'type' => $type,
            'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
        ];

        return view('Admin/signalements', $data);
    }

    /**
     * Traiter un signalement (accepter ou rejeter)
     *
     * Permet de marquer un signalement comme 'traite' ou 'rejete'
     * avec une raison optionnelle via signalement_traiter()
     *
     * URL: POST /admin/traiterSignalement
     * Paramètres POST: id_signalement, action (traite|rejete), raison (optionnel)
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers /admin/signalements avec message flash
     */
    public function traiterSignalement()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idSignalement = $this->request->getPost('id_signalement');
        $action = $this->request->getPost('action'); // 'traite' ou 'rejete'
        $raison = $this->request->getPost('raison');

        // Validation
        if (empty($idSignalement) || empty($action)) {
            session()->setFlashdata('error', 'Paramètres manquants');
            return redirect()->to(base_url('admin/signalements'));
        }

        if (!in_array($action, ['traite', 'rejete'])) {
            session()->setFlashdata('error', 'Action invalide');
            return redirect()->to(base_url('admin/signalements'));
        }

        // Traiter le signalement
        $result = $this->signalementModel->traiterSignalement(
            (int)$idSignalement,
            $action,
            $raison
        );

        if ($result) {
            $message = $action === 'traite' 
                ? 'Le signalement a été traité avec succès' 
                : 'Le signalement a été rejeté';
            session()->setFlashdata('success', $message);
        } else {
            session()->setFlashdata('error', 'Erreur lors du traitement du signalement');
        }

        return redirect()->to(base_url('admin/signalements'));
    }

    /**
     * Afficher la page de gestion des utilisateurs
     *
     * Liste tous les utilisateurs avec filtres :
     * - Par type de compte : standard, admin, tous
     * - Par statut : actif, suspendu, bannis, tous
     * - Par recherche : nom, prénom, email
     *
     * Exigence 31 : Gestion des comptes utilisateurs par l'admin
     *
     * URL: /admin/utilisateurs
     * Paramètres GET: type_compte, status, search
     *
     * @return string Vue de gestion des utilisateurs
     */
    public function utilisateurs()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        // Récupérer les filtres
        $typeCompte = $this->request->getGet('type_compte') ?? 'tous';
        $status = $this->request->getGet('status') ?? 'tous';
        $search = $this->request->getGet('search') ?? '';
        $page = (int) ($this->request->getGet('page') ?? 1);

        $filters = [];
        if ($typeCompte !== 'tous') {
            $filters['type_compte'] = $typeCompte;
        }
        if ($status !== 'tous') {
            $filters['status'] = $status;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }

        // Récupérer les utilisateurs avec pagination (20 par page)
        $utilisateurModel = new UtilisateurModel();
        $result = $utilisateurModel->listerUtilisateurs($filters, $page, 20);

        $data = [
            'utilisateurs' => $result['utilisateurs'],
            'pagination' => $result['pagination'],
            'type_compte_actuel' => $typeCompte,
            'status_actuel' => $status,
            'search' => $search,
            'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
        ];

        return view('Admin/utilisateurs', $data);
    }

    /**
     * Suspendre un utilisateur
     *
     * Change le statut à 'suspendu', traite tous les signalements en attente
     * et enregistre l'action dans ADMIN_LOG
     * L'utilisateur ne pourra plus se connecter jusqu'à réactivation
     *
     * Exigence 31 : Modération des comptes
     *
     * URL: POST /admin/suspendreUtilisateur
     * Paramètres POST: id_utilisateur, raison
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers /admin/utilisateurs
     */
    public function suspendreUtilisateur()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idUtilisateur = $this->request->getPost('id_utilisateur');
        $raison = $this->request->getPost('raison');

        if (empty($idUtilisateur)) {
            session()->setFlashdata('error', 'ID utilisateur manquant');
            return redirect()->to(base_url('admin/utilisateurs'));
        }

        $utilisateurModel = new UtilisateurModel();
        $result = $utilisateurModel->changerStatus($idUtilisateur, 'suspendu');

        if ($result) {
            // Traiter tous les signalements en attente concernant cet utilisateur
            $this->signalementModel->traiterSignalementsUtilisateur(
                $idUtilisateur,
                "Compte suspendu par l'administrateur. Raison: " . ($raison ?? 'Non spécifiée')
            );

            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'suspension_utilisateur',
                $idUtilisateur,
                $raison,
                $this->request->getIPAddress()
            );

            session()->setFlashdata('success', 'Utilisateur suspendu avec succès et signalements traités');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la suspension de l\'utilisateur');
        }

        return redirect()->to(base_url('admin/utilisateurs'));
    }

    /**
     * Bannir un utilisateur définitivement
     *
     * Change le statut à 'bannis' (bannissement permanent)
     * Traite tous les signalements et enregistre l'action dans ADMIN_LOG
     * L'utilisateur ne pourra plus jamais se connecter
     *
     * Exigence 31 : Modération des comptes
     *
     * URL: POST /admin/bannirUtilisateur
     * Paramètres POST: id_utilisateur, raison
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers /admin/utilisateurs
     */
    public function bannirUtilisateur()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idUtilisateur = $this->request->getPost('id_utilisateur');
        $raison = $this->request->getPost('raison');

        if (empty($idUtilisateur)) {
            session()->setFlashdata('error', 'ID utilisateur manquant');
            return redirect()->to(base_url('admin/utilisateurs'));
        }

        $utilisateurModel = new UtilisateurModel();
        $result = $utilisateurModel->changerStatus($idUtilisateur, 'bannis');

        if ($result) {
            // Traiter tous les signalements en attente concernant cet utilisateur
            $this->signalementModel->traiterSignalementsUtilisateur(
                $idUtilisateur,
                "Compte banni définitivement par l'administrateur. Raison: " . ($raison ?? 'Non spécifiée')
            );

            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'bannissement_utilisateur',
                $idUtilisateur,
                $raison,
                $this->request->getIPAddress()
            );

            session()->setFlashdata('success', 'Utilisateur banni avec succès et signalements traités');
        } else {
            session()->setFlashdata('error', 'Erreur lors du bannissement de l\'utilisateur');
        }

        return redirect()->to(base_url('admin/utilisateurs'));
    }

    /**
     * Réactiver un utilisateur suspendu ou banni
     *
     * Change le statut vers 'actif' et enregistre l'action dans ADMIN_LOG
     * Permet à l'utilisateur de se reconnecter
     *
     * Exigence 31 : Gestion des comptes
     *
     * URL: POST /admin/reactiverUtilisateur
     * Paramètres POST: id_utilisateur, raison
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers /admin/utilisateurs
     */
    public function reactiverUtilisateur()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idUtilisateur = $this->request->getPost('id_utilisateur');
        $raison = $this->request->getPost('raison');

        if (empty($idUtilisateur)) {
            session()->setFlashdata('error', 'ID utilisateur manquant');
            return redirect()->to(base_url('admin/utilisateurs'));
        }

        $utilisateurModel = new UtilisateurModel();
        $result = $utilisateurModel->changerStatus($idUtilisateur, 'actif');

        if ($result) {
            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'reactivation_utilisateur',
                $idUtilisateur,
                $raison,
                $this->request->getIPAddress()
            );

            session()->setFlashdata('success', 'Utilisateur réactivé avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la réactivation de l\'utilisateur');
        }

        return redirect()->to(base_url('admin/utilisateurs'));
    }

    /**
     * Supprimer un utilisateur (Exigence 31)
     */
    public function supprimerUtilisateur()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idUtilisateur = $this->request->getPost('id_utilisateur');
        $raison = $this->request->getPost('raison');

        if (empty($idUtilisateur)) {
            session()->setFlashdata('error', 'ID utilisateur manquant');
            return redirect()->to(base_url('admin/utilisateurs'));
        }

        // Traiter tous les signalements en attente AVANT la suppression
        $this->signalementModel->traiterSignalementsUtilisateur(
            $idUtilisateur,
            "Compte supprimé définitivement par l'administrateur. Raison: " . ($raison ?? 'Non spécifiée')
        );

        $utilisateurModel = new UtilisateurModel();
        $result = $utilisateurModel->supprimerCompte($idUtilisateur);

        if ($result) {
            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'suppression_utilisateur',
                $idUtilisateur,
                $raison,
                $this->request->getIPAddress()
            );

            session()->setFlashdata('success', 'Utilisateur supprimé avec succès et signalements traités');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la suppression de l\'utilisateur');
        }

        return redirect()->to(base_url('admin/utilisateurs'));
    }

    /**
     * Page de gestion des annonces signalées (Exigence 32)
     */
    public function annoncesSignalees()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        // Récupérer les annonces signalées
        $annoncesSignalees = $this->adminModel->getAnnoncesSignalees();

        $data = [
            'annonces' => $annoncesSignalees,
            'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
        ];

        return view('Admin/annonces_signalees', $data);
    }

    /**
     * Voir les détails d'une annonce signalée
     */
    public function detailSignalementAnnonce($idAnnonce)
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        // Récupérer les détails de l'annonce avec signalements
        $annonce = $this->adminModel->getAnnonceSignaleeDetails($idAnnonce);

        if (empty($annonce)) {
            session()->setFlashdata('error', 'Annonce introuvable');
            return redirect()->to(base_url('admin/signalements'));
        }

        $data = [
            'annonce' => $annonce,
            'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
        ];

        return view('Admin/annonce_details', $data);
    }

    /**
     * Supprimer une annonce signalée (Exigence 32)
     */
    public function supprimerAnnonce()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idAnnonce = $this->request->getPost('id_annonce');
        $raison = $this->request->getPost('raison');

        if (empty($idAnnonce)) {
            session()->setFlashdata('error', 'ID annonce manquant');
            return redirect()->to(base_url('admin/signalements'));
        }

        // Utiliser le modèle AnnoncesModel pour supprimer
        $annoncesModel = new \App\Models\AnnoncesModel();
        $result = $annoncesModel->supprimer($idAnnonce);

        if ($result) {
            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'suppression_annonce',
                $idAnnonce,
                $raison,
                $this->request->getIPAddress()
            );

            // Marquer tous les signalements liés comme traités
            $this->signalementModel->traiterSignalementsAnnonce($idAnnonce, 'traite');

            session()->setFlashdata('success', 'Annonce supprimée avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la suppression de l\'annonce');
        }

        return redirect()->to(base_url('admin/signalements'));
    }

    /**
     * Rejeter les signalements d'une annonce (conserver l'annonce)
     */
    public function rejeterSignalementsAnnonce()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idAnnonce = $this->request->getPost('id_annonce');
        $raison = $this->request->getPost('raison');

        if (empty($idAnnonce)) {
            session()->setFlashdata('error', 'ID annonce manquant');
            return redirect()->to(base_url('admin/signalements'));
        }

        // Rejeter tous les signalements liés à cette annonce
        $result = $this->signalementModel->traiterSignalementsAnnonce($idAnnonce, 'rejete', $raison);

        if ($result) {
            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'rejet_signalement_annonce',
                $idAnnonce,
                $raison,
                $this->request->getIPAddress()
            );

            session()->setFlashdata('success', 'Signalements rejetés, l\'annonce a été conservée');
        } else {
            session()->setFlashdata('error', 'Erreur lors du rejet des signalements');
        }

        return redirect()->to(base_url('admin/signalements'));
    }

    /**
     * Voir les détails d'un signalement de review (Exigence 33)
     * 
     * Affiche les détails d'une review signalée avec :
     * - Informations de la review (note, commentaire, date)
     * - Informations de l'auteur de la review
     * - Informations du vendeur concerné
     * - Liste des signalements avec détails
     * 
     * URL: /admin/signalement/review/{idReview}
     * 
     * @param string $idReview UUID de la review
     * @return string|\CodeIgniter\HTTP\RedirectResponse Vue détaillée ou redirection si erreur
     */
    public function detailSignalementReview($idReview)
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        // Récupérer les détails de la review avec signalements
        $review = $this->adminModel->getReviewSignaleeDetails($idReview);

        if (empty($review)) {
            session()->setFlashdata('error', 'Avis introuvable');
            return redirect()->to(base_url('admin/signalements?type=review'));
        }

        $data = [
            'review' => $review,
            'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
        ];

        return view('Admin/review_details', $data);
    }

    /**
     * Supprimer une review signalée (Exigence 33)
     * 
     * Supprime la review et marque tous les signalements associés comme traités
     * Log l'action dans ADMIN_LOG
     * 
     * URL: POST /admin/supprimerReview
     * Paramètres POST: id_review, raison
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers /admin/signalements
     */
    public function supprimerReview()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idReview = $this->request->getPost('id_review');
        $raison = $this->request->getPost('raison');

        if (empty($idReview)) {
            session()->setFlashdata('error', 'ID de l\'avis manquant');
            return redirect()->to(base_url('admin/signalements?type=review'));
        }

        // Utiliser le ReviewModel pour supprimer
        $reviewModel = new \App\Models\ReviewModel();
        $result = $reviewModel->supprimerAvis($idReview);

        if ($result) {
            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'suppression_review',
                $idReview,
                $raison,
                $this->request->getIPAddress()
            );

            // Marquer tous les signalements liés comme traités
            $this->signalementModel->traiterSignalementsReview(
                $idReview, 
                'traite', 
                'Avis supprimé par l\'administrateur. Raison: ' . ($raison ?? 'Non spécifiée')
            );

            session()->setFlashdata('success', 'Avis supprimé avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la suppression de l\'avis');
        }

        return redirect()->to(base_url('admin/signalements?type=review'));
    }

    /**
     * Rejeter les signalements d'une review (conserver la review)
     * 
     * Marque tous les signalements liés à cette review comme rejetés
     * La review est conservée
     * 
     * URL: POST /admin/rejeterSignalementsReview
     * Paramètres POST: id_review, raison
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection vers /admin/signalements
     */
    public function rejeterSignalementsReview()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idReview = $this->request->getPost('id_review');
        $raison = $this->request->getPost('raison');

        if (empty($idReview)) {
            session()->setFlashdata('error', 'ID de l\'avis manquant');
            return redirect()->to(base_url('admin/signalements?type=review'));
        }

        // Rejeter tous les signalements liés à cette review
        $result = $this->signalementModel->traiterSignalementsReview($idReview, 'rejete', $raison);

        if ($result) {
            // Logger l'action
            $this->adminModel->logAction(
                session()->get('user_id'),
                'rejet_signalement_review',
                $idReview,
                $raison,
                $this->request->getIPAddress()
            );

            session()->setFlashdata('success', 'Signalements rejetés, l\'avis a été conservé');
        } else {
            session()->setFlashdata('error', 'Erreur lors du rejet des signalements');
        }

        return redirect()->to(base_url('admin/signalements?type=review'));
    }

    /**
     * Voir les détails d'un signalement de compte
     */
    public function detailSignalementCompte($idCompte)
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        // Récupérer les détails de l'utilisateur avec signalements
        $utilisateur = $this->adminModel->getUtilisateurSignaleDetails($idCompte);

        if (empty($utilisateur)) {
            session()->setFlashdata('error', 'Utilisateur introuvable');
            return redirect()->to(base_url('admin/signalements'));
        }

        $data = [
            'utilisateur' => $utilisateur,
            'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
        ];

        return view('Admin/utilisateur_details', $data);
    }

    /**
     * Voir les détails d'un utilisateur (alias pour detailSignalementCompte)
     * Accessible depuis la liste des utilisateurs
     */
    public function detailUtilisateur($idUtilisateur)
    {
        return $this->detailSignalementCompte($idUtilisateur);
    }

    /**
     * Afficher la page de gestion des attributs produits
     *
     * Permet de gérer les marques, couleurs, matériaux et système de tailles
     *
     * URL: /admin/attributs
     *
     * @return string Vue de gestion des attributs
     */
    public function attributs()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $marqueModel = new MarqueModel();
        $couleurModel = new CouleurModel();
        $materiauModel = new MateriauModel();

        $data = [
            'marques' => $marqueModel->listerMarques(),
            'couleurs' => $couleurModel->listerCouleurs(),
            'materiaux' => $materiauModel->listerMateriaux(),
            'systemes_taille' => ['EU', 'US', 'UK'],
            'admin_nom' => session()->get('user_prenom') . ' ' . session()->get('user_nom')
        ];

        return view('Admin/attributs', $data);
    }

    /**
     * Ajouter une nouvelle marque
     *
     * URL: POST /admin/ajouterMarque
     * Paramètres POST: nom
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function ajouterMarque()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $nom = trim($this->request->getPost('nom'));

        if (empty($nom)) {
            session()->setFlashdata('error', 'Le nom de la marque est requis');
            return redirect()->to(base_url('admin/attributs'));
        }

        $marqueModel = new MarqueModel();
        $result = $marqueModel->creerMarque($nom);

        if ($result) {
            session()->setFlashdata('success', 'Marque ajoutée avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de l\'ajout de la marque (peut-être existe-t-elle déjà)');
        }

        return redirect()->to(base_url('admin/attributs'));
    }

    /**
     * Ajouter une nouvelle couleur
     *
     * URL: POST /admin/ajouterCouleur
     * Paramètres POST: nom
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function ajouterCouleur()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $nom = trim($this->request->getPost('nom'));

        if (empty($nom)) {
            session()->setFlashdata('error', 'Le nom de la couleur est requis');
            return redirect()->to(base_url('admin/attributs'));
        }

        $couleurModel = new CouleurModel();
        $result = $couleurModel->creerCouleur($nom);

        if ($result) {
            session()->setFlashdata('success', 'Couleur ajoutée avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de l\'ajout de la couleur (peut-être existe-t-elle déjà)');
        }

        return redirect()->to(base_url('admin/attributs'));
    }

    /**
     * Ajouter un nouveau matériau
     *
     * URL: POST /admin/ajouterMateriau
     * Paramètres POST: nom
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function ajouterMateriau()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $nom = trim($this->request->getPost('nom'));

        if (empty($nom)) {
            session()->setFlashdata('error', 'Le nom du matériau est requis');
            return redirect()->to(base_url('admin/attributs'));
        }

        $materiauModel = new MateriauModel();
        $result = $materiauModel->creerMateriau($nom);

        if ($result) {
            session()->setFlashdata('success', 'Matériau ajouté avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de l\'ajout du matériau (peut-être existe-t-il déjà)');
        }

        return redirect()->to(base_url('admin/attributs'));
    }

    /**
     * Supprimer une marque
     *
     * URL: POST /admin/supprimerMarque
     * Paramètres POST: id_marque
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function supprimerMarque()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idMarque = $this->request->getPost('id_marque');

        if (empty($idMarque)) {
            session()->setFlashdata('error', 'ID marque manquant');
            return redirect()->to(base_url('admin/attributs'));
        }

        $marqueModel = new MarqueModel();
        $result = $marqueModel->supprimerMarque($idMarque);

        if ($result) {
            session()->setFlashdata('success', 'Marque supprimée avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la suppression (peut-être utilisée par des annonces)');
        }

        return redirect()->to(base_url('admin/attributs'));
    }

    /**
     * Supprimer une couleur
     *
     * URL: POST /admin/supprimerCouleur
     * Paramètres POST: id_couleur
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function supprimerCouleur()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idCouleur = $this->request->getPost('id_couleur');

        if (empty($idCouleur)) {
            session()->setFlashdata('error', 'ID couleur manquant');
            return redirect()->to(base_url('admin/attributs'));
        }

        $couleurModel = new CouleurModel();
        $result = $couleurModel->supprimerCouleur($idCouleur);

        if ($result) {
            session()->setFlashdata('success', 'Couleur supprimée avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la suppression (peut-être utilisée par des annonces)');
        }

        return redirect()->to(base_url('admin/attributs'));
    }

    /**
     * Supprimer un matériau
     *
     * URL: POST /admin/supprimerMateriau
     * Paramètres POST: id_materiau
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function supprimerMateriau()
    {
        // Vérifier l'authentification
        $redirect = $this->checkAdminAuth();
        if ($redirect) return $redirect;

        $idMateriau = $this->request->getPost('id_materiau');

        if (empty($idMateriau)) {
            session()->setFlashdata('error', 'ID matériau manquant');
            return redirect()->to(base_url('admin/attributs'));
        }

        $materiauModel = new MateriauModel();
        $result = $materiauModel->supprimerMateriau($idMateriau);

        if ($result) {
            session()->setFlashdata('success', 'Matériau supprimé avec succès');
        } else {
            session()->setFlashdata('error', 'Erreur lors de la suppression (peut-être utilisé par des annonces)');
        }

        return redirect()->to(base_url('admin/attributs'));
    }
}

