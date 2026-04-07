<?php

namespace App\Controllers;

use App\Models\AnnoncesModel;
use App\Models\UtilisateurModel;
use App\Models\ReviewModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Utilisateur extends BaseController
{
    protected $annoncesModel;
    protected $utilisateurModel;

    public function __construct()
    {
        $this->annoncesModel = new AnnoncesModel();
        $this->utilisateurModel = new UtilisateurModel();
    }

    /**
     * Afficher le profil d'un utilisateur
     *
     * URL: /utilisateur/profil/{id}
     * Exemple: /utilisateur/profil/123
     *
     * @param string $id ID de l'utilisateur
     */
    public function profil(string $id)
    {
        $utilisateur = $this->utilisateurModel->getUtilisateur($id);

        if (!$utilisateur) {
            throw PageNotFoundException::forPageNotFound("Utilisateur $id introuvable");
        }

        // ===== PAGINATION pour les annonces de l'utilisateur =====
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        // Compter le nombre total d'annonces de cet utilisateur
        $db = \Config\Database::connect();
        $countQuery = $db->query(
            "SELECT COUNT(*) as total FROM annonce_list_by_vendeur(?) a WHERE a.disponible = true",
            [$id]
        );
        $totalAnnonces = $countQuery->getRowArray()['total'];
        $totalPages = ceil($totalAnnonces / $perPage);

        // Récupérer les annonces de cet utilisateur via le modèle (avec images)
        $userAnnonces = $db->query(
            "SELECT 
                a.*,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom,
                i.url as image_url,
                i.est_principale as image_principale
             FROM annonce_list_by_vendeur(?) a
             LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
             LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
             LEFT JOIN marque br ON a.id_marque = br.id_marque
             LEFT JOIN image i ON a.id_image = i.id_image
             WHERE a.disponible = true
             ORDER BY a.date_publication DESC
             LIMIT ? OFFSET ?",
            [$id, $perPage, $offset]
        )->getResultArray();

        // ===== REVIEWS SECTION =====
        $reviewModel = new ReviewModel();

        // Check if logged-in user has purchased from this seller
        $hasPurchased = false;
        $userReview = null;
        if (session()->get('is_logged_in') && session()->get('user_type_compte') !== 'admin') {
            $currentUserId = session()->get('user_id');

            // Check if current user has purchased from this seller
            $purchaseCheck = $db->query(
                "SELECT COUNT(DISTINCT c.id_commande) as purchase_count
                 FROM COMMANDE c
                 JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
                 JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
                 WHERE c.id_utilisateur = ? AND a.id_utilisateur_vendeur = ? AND c.statut != 'annulee'",
                [$currentUserId, $id]
            )->getRowArray();

            $hasPurchased = ($purchaseCheck['purchase_count'] ?? 0) > 0;

            // Get user's own review if exists
            $userReview = $reviewModel->getAvisExistant($currentUserId, $id);
        }

        // Pagination for reviews
        $reviewsPerPage = 10;
        $reviewPage = (int) ($this->request->getGet('review_page') ?? 1);
        $reviewPage = max(1, $reviewPage);
        $reviewOffset = ($reviewPage - 1) * $reviewsPerPage;

        // Get review statistics from cached fields (optimized)
        $totalReviews = (int) $utilisateur['nombre_avis'];
        $averageRating = (float) $utilisateur['note_moyenne'];

        // Count other reviews (excluding user's own review if exists)
        $excludeReviewId = $userReview ? $userReview['id_review'] : null;
        $totalOtherReviews = $reviewModel->countAvisVendeur($id, $excludeReviewId);
        $totalReviewPages = ceil($totalOtherReviews / $reviewsPerPage);

        // Get paginated reviews from database (SQL-level pagination, optimized)
        $paginatedReviews = $reviewModel->getAvisVendeurPaginated($id, $reviewsPerPage, $reviewOffset, $excludeReviewId);

        $data = [
            'title' => 'Profil de ' . $utilisateur['prenom'] . ' ' . $utilisateur['nom'],
            'user' => $utilisateur,
            'userAnnonces' => $userAnnonces,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalAnnonces
            ],
            'hasPurchased' => $hasPurchased,
            'userReview' => $userReview,
            'reviews' => $paginatedReviews,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'reviewPagination' => [
                'current_page' => $reviewPage,
                'total_pages' => $totalReviewPages,
                'per_page' => $reviewsPerPage,
                'total_items' => $totalOtherReviews
            ]
        ];

        return view('Users/profil', $data);
    }

    /**
     * Page de gestion du compte utilisateur
     */
    public function compte(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté pour accéder à votre compte.');
            return redirect()->to(base_url('connexion'));
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $typeCompte = session()->get('user_type_compte');

        // Récupérer les adresses de l'utilisateur (uniquement pour les utilisateurs standard)
        $address = null;
        if ($typeCompte !== 'admin') {
            $addresses = $db->query("SELECT * FROM adresse_list_by_user(?)", [$userId])->getResultArray();
            $address = !empty($addresses) ? $addresses[0] : null;
        }

        $data = [
            'title' => 'Mon compte',
            'user' => [
                'id_utilisateur' => $userId,
                'prenom' => session()->get('user_prenom'),
                'nom' => session()->get('user_nom'),
                'email' => session()->get('user_email'),
                'type_compte' => $typeCompte,
            ],
            'address' => $address
        ];

        return view('Users/compte', $data);
    }

    /**
     * Traiter la modification du compte (POST)
     */
    public function updateCompte(): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté.');
            return redirect()->to(base_url('connexion'));
        }

        $userId = session()->get('user_id');

        // Récupérer les données du formulaire
        $prenom = $this->request->getPost('prenom');
        $nom = $this->request->getPost('nom');
        $email = $this->request->getPost('email');
        $motDePasse = $this->request->getPost('mot_de_passe');
        $motDePasseConfirm = $this->request->getPost('mot_de_passe_confirmation');
        $motDePasseActuel = $this->request->getPost('mot_de_passe_actuel');

        // Validation basique
        if (empty($prenom) || empty($nom) || empty($email)) {
            session()->setFlashdata('error', 'Le prénom, nom et email sont obligatoires.');
            return redirect()->to(base_url('compte'))->withInput();
        }

        // Vérifier le mot de passe actuel (sécurité)
        if (empty($motDePasseActuel)) {
            session()->setFlashdata('error', 'Veuillez entrer votre mot de passe actuel pour confirmer les modifications.');
            return redirect()->to(base_url('compte'))->withInput();
        }

        // Récupérer l'utilisateur complet pour vérifier le mot de passe
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM utilisateur_read(?)", [$userId]);
        $currentUser = $query->getRowArray();

        if (!$currentUser || !password_verify($motDePasseActuel, $currentUser['mdp'])) {
            session()->setFlashdata('error', 'Mot de passe actuel incorrect.');
            return redirect()->to(base_url('compte'))->withInput();
        }

        // Préparer les données utilisateur
        $userData = [
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email
        ];

        // Si mot de passe fourni, le valider et l'ajouter
        if (!empty($motDePasse)) {
            if ($motDePasse !== $motDePasseConfirm) {
                session()->setFlashdata('error', 'Les mots de passe ne correspondent pas.');
                return redirect()->to(base_url('compte'))->withInput();
            }

            if (strlen($motDePasse) < 8) {
                session()->setFlashdata('error', 'Le mot de passe doit contenir au moins 8 caractères.');
                return redirect()->to(base_url('compte'))->withInput();
            }

            // Hacher le mot de passe
            $userData['mdp'] = password_hash($motDePasse, PASSWORD_BCRYPT);
        }

        // Mettre à jour les informations utilisateur via le modèle
        $updatedUser = $this->utilisateurModel->modifierInformations($userId, $userData);

        if (!$updatedUser) {
            session()->setFlashdata('error', 'Erreur lors de la mise à jour du compte.');
            return redirect()->to(base_url('compte'))->withInput();
        }

        // Mettre à jour la session avec les nouvelles données
        session()->set([
            'user_prenom' => $updatedUser['prenom'],
            'user_nom' => $updatedUser['nom'],
            'user_email' => $updatedUser['email']
        ]);

        // Traiter l'adresse si fournie (uniquement pour les utilisateurs standard)
        if (session()->get('user_type_compte') !== 'admin') {
            $rue1 = $this->request->getPost('rue1');
            $rue2 = $this->request->getPost('rue2');
            $codePostal = $this->request->getPost('code_postal');
            $ville = $this->request->getPost('ville');
            $pays = $this->request->getPost('pays');

            if (!empty($rue1) && !empty($codePostal) && !empty($ville)) {
                $addressData = [
                    'rue1' => $rue1,
                    'rue2' => $rue2 ?? '',
                    'code_postal' => $codePostal,
                    'ville' => $ville,
                    'pays' => $pays ?? 'France'
                ];

                $db = \Config\Database::connect();
                $addresses = $db->query("SELECT * FROM adresse_list_by_user(?)", [$userId])->getResultArray();

                if (!empty($addresses)) {
                    // Mettre à jour l'adresse existante
                    $this->utilisateurModel->modifierAdresse($addresses[0]['id_adresse'], $addressData);
                } else {
                    // Créer une nouvelle adresse
                    $this->utilisateurModel->ajouterAdresse($userId, $addressData);
                }
            }
        }

        session()->setFlashdata('success', 'Votre compte a été mis à jour avec succès.');
        return redirect()->to(base_url('compte'));
    }

    /**
     * Page des commandes utilisateur
     */
    public function commandes(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté pour accéder à vos commandes.');
            return redirect()->to(base_url('connexion'));
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Pagination
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Compter le total de commandes
        $countQuery = $db->query("
            SELECT COUNT(DISTINCT c.id_commande) as total
            FROM COMMANDE c
            JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
            JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
            WHERE c.id_utilisateur = ?
        ", [$userId]);
        $totalCommandes = $countQuery->getRowArray()['total'];
        $totalPages = ceil($totalCommandes / $perPage);

        // Récupérer les commandes avec détails (paginées)
        $commandesQuery = $db->query("
            SELECT
                c.id_commande,
                c.date,
                c.statut,
                p.type as type_paiement,
                p.statut as statut_paiement,
                p.montant_paye,
                a.id_annonce,
                a.titre as annonce_titre,
                a.prix as annonce_prix,
                i.url as image_url,
                lc.quantite,
                u.id_utilisateur as vendeur_id,
                u.prenom as vendeur_prenom,
                u.nom as vendeur_nom,
                r.id_review,
                r.note,
                r.commentaire
            FROM COMMANDE c
            JOIN PAIEMENT p ON c.id_paiement = p.id_paiement
            JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
            JOIN LIGNE_COMMANDE lc ON dc.id_ligne_commande = lc.id_ligne_commande
            JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
            JOIN UTILISATEUR u ON a.id_utilisateur_vendeur = u.id_utilisateur
            LEFT JOIN IMAGE i ON a.id_image = i.id_image
            LEFT JOIN REVIEW r ON r.id_utilisateur_auteur = ? AND r.id_utilisateur_vendeur = u.id_utilisateur
            WHERE c.id_utilisateur = ?
            ORDER BY c.date DESC
            LIMIT ? OFFSET ?
        ", [$userId, $userId, $perPage, $offset]);

        $commandes = $commandesQuery->getResultArray();

        $data = [
            'title' => 'Mes commandes',
            'user' => [
                'id_utilisateur' => $userId,
                'prenom' => session()->get('user_prenom'),
                'nom' => session()->get('user_nom'),
                'email' => session()->get('user_email'),
                'type_compte' => session()->get('user_type_compte'),
            ],
            'commandes' => $commandes,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalCommandes
            ]
        ];

        return view('Users/commandes', $data);
    }

    /**
     * Page des ventes utilisateur
     */
    public function ventes(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté pour accéder à vos ventes.');
            return redirect()->to(base_url('connexion'));
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Pagination
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Compter le total de ventes
        $countQuery = $db->query("
            SELECT COUNT(DISTINCT c.id_commande) as total
            FROM ANNONCE a
            JOIN DETAILLER_COMMANDE dc ON a.id_annonce = dc.id_annonce
            JOIN COMMANDE c ON dc.id_commande = c.id_commande
            WHERE a.id_utilisateur_vendeur = ?
        ", [$userId]);
        $totalVentes = $countQuery->getRowArray()['total'];
        $totalPages = ceil($totalVentes / $perPage);

        // Récupérer les ventes (produits vendus par cet utilisateur) - paginées
        $ventesQuery = $db->query("
            SELECT
                c.id_commande,
                c.date,
                c.statut as statut_commande,
                p.type as type_paiement,
                p.statut as statut_paiement,
                p.montant_paye,
                a.id_annonce,
                a.titre as annonce_titre,
                a.prix as annonce_prix,
                i.url as image_url,
                lc.quantite,
                u.id_utilisateur as acheteur_id,
                u.prenom as acheteur_prenom,
                u.nom as acheteur_nom
            FROM ANNONCE a
            JOIN DETAILLER_COMMANDE dc ON a.id_annonce = dc.id_annonce
            JOIN LIGNE_COMMANDE lc ON dc.id_ligne_commande = lc.id_ligne_commande
            JOIN COMMANDE c ON dc.id_commande = c.id_commande
            JOIN PAIEMENT p ON c.id_paiement = p.id_paiement
            JOIN UTILISATEUR u ON c.id_utilisateur = u.id_utilisateur
            LEFT JOIN IMAGE i ON a.id_image = i.id_image
            WHERE a.id_utilisateur_vendeur = ?
            ORDER BY c.date DESC
            LIMIT ? OFFSET ?
        ", [$userId, $perPage, $offset]);

        $ventes = $ventesQuery->getResultArray();

        // Récupérer les statistiques de vente depuis les champs cachés (optimisé, O(1))
        $utilisateur = $this->utilisateurModel->getUtilisateur($userId);

        $stats = [
            'montant_mois' => $utilisateur['montant_mois_actuel'] ?? 0,
            'montant_annee' => $utilisateur['montant_annee_actuelle'] ?? 0,
            'montant_total' => $utilisateur['montant_total_ventes'] ?? 0
        ];

        $data = [
            'title' => 'Mes ventes',
            'user' => [
                'id_utilisateur' => $userId,
                'prenom' => session()->get('user_prenom'),
                'nom' => session()->get('user_nom'),
                'email' => session()->get('user_email'),
                'type_compte' => session()->get('user_type_compte'),
            ],
            'ventes' => $ventes,
            'stats' => $stats,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalVentes
            ]
        ];

        return view('Users/ventes', $data);
    }

    /**
     * Mettre à jour le statut d'une commande (pour les vendeurs)
     */
    public function updateStatutCommande(): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté.');
            return redirect()->to(base_url('connexion'));
        }

        if (session()->get('user_type_compte') === 'admin') {
            session()->setFlashdata('error', 'Accès non autorisé.');
            return redirect()->to(base_url('admin/dashboard'));
        }

        $idCommande = $this->request->getPost('id_commande');
        $nouveauStatut = $this->request->getPost('statut');

        // Validation
        $statutsValides = ['en_preparation', 'expediee', 'livree'];
        if (!in_array($nouveauStatut, $statutsValides)) {
            session()->setFlashdata('error', 'Statut invalide.');
            return redirect()->to(base_url('ventes'));
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Vérifier que la commande appartient à une annonce du vendeur
        $verificationQuery = $db->query("
            SELECT c.id_commande
            FROM COMMANDE c
            JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
            JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
            WHERE c.id_commande = ? AND a.id_utilisateur_vendeur = ?
        ", [$idCommande, $userId]);

        if ($verificationQuery->getNumRows() === 0) {
            session()->setFlashdata('error', 'Vous n\'êtes pas autorisé à modifier cette commande.');
            return redirect()->to(base_url('ventes'));
        }

        // Mettre à jour le statut via la procédure stockée
        try {
            $db->query("SELECT commande_update_statut(?::uuid, ?)", [$idCommande, $nouveauStatut]);
            session()->setFlashdata('success', 'Statut de la commande mis à jour avec succès.');
        } catch (\Exception $e) {
            log_message('error', 'Erreur update statut commande: ' . $e->getMessage());
            session()->setFlashdata('error', 'Erreur lors de la mise à jour du statut.');
        }

        return redirect()->to(base_url('ventes'));
    }
}
