<?php

namespace App\Controllers;

use App\Models\AnnoncesModel;
use App\Models\CouleurModel;
use App\Models\MarqueModel;
use App\Models\MateriauModel;
use CodeIgniter\HTTP\RedirectResponse;

class Vendre extends BaseController
{

    /**
     * Afficher la page de vente (formulaire onboarding)
     *
     * URL: /vendre
     *
     * @return string|RedirectResponse Vue de la page de vente ou redirection si non connecté
     */
    public function index(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('connexion'))->with('error', 'Vous devez être connecté pour vendre un produit');
        }

        try {
            $couleurModel = new CouleurModel();
            $marqueModel = new MarqueModel();
            $materiauModel = new MateriauModel();

            $couleurs = $couleurModel->listerCouleurs();
            $marques = $marqueModel->listerMarques();
            $materiaux = $materiauModel->listerMateriaux();

            return view('Vente/page', [
                'couleurs' => $couleurs,
                'marques' => $marques,
                'materiaux' => $materiaux
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur dans Vendre::index: ' . $e->getMessage());
            return redirect()->to(base_url('/'))->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Traiter la soumission du formulaire de vente
     *
     * URL: POST /vendre
     *
     * @return RedirectResponse
     */
    public function publier(): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('connexion'))->with('error', 'Vous devez être connecté pour vendre un produit');
        }

        try {
            $userId = session()->get('user_id');
            $annoncesModel = new AnnoncesModel();
            $validation = \Config\Services::validation();

            // Règles de validation avec messages en français
            $validation->setRules([
                'titre' => [
                    'rules' => 'required|min_length[3]|max_length[200]',
                    'errors' => [
                        'required' => 'Le titre est obligatoire',
                        'min_length' => 'Le titre doit contenir au moins 3 caractères',
                        'max_length' => 'Le titre ne peut pas dépasser 200 caractères'
                    ]
                ],
                'description' => [
                    'rules' => 'required|min_length[10]',
                    'errors' => [
                        'required' => 'La description est obligatoire',
                        'min_length' => 'La description doit contenir au moins 10 caractères'
                    ]
                ],
                'prix' => [
                    'rules' => 'required|decimal|greater_than[0]',
                    'errors' => [
                        'required' => 'Le prix est obligatoire',
                        'decimal' => 'Le prix doit être un nombre valide',
                        'greater_than' => 'Le prix doit être supérieur à 0'
                    ]
                ],
                'etat' => [
                    'rules' => 'required|in_list[neuf,tres_bon,bon,correct]',
                    'errors' => [
                        'required' => 'L\'état est obligatoire',
                        'in_list' => 'L\'état sélectionné n\'est pas valide'
                    ]
                ],
                'taille' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'La taille est obligatoire']
                ],
                'taille_systeme' => [
                    'rules' => 'required|in_list[EU,US,UK]',
                    'errors' => [
                        'required' => 'Le système de taille est obligatoire',
                        'in_list' => 'Le système de taille n\'est pas valide'
                    ]
                ],
                'id_couleur' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'La couleur est obligatoire']
                ],
                'id_marque' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'La marque est obligatoire']
                ],
                'id_materiau' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Le matériau est obligatoire']
                ],
                'image' => [
                    'rules' => 'uploaded[image]|max_size[image,5120]|is_image[image]',
                    'errors' => [
                        'uploaded' => 'L\'image est obligatoire',
                        'max_size' => 'L\'image ne doit pas dépasser 5 MB',
                        'is_image' => 'Le fichier doit être une image valide'
                    ]
                ]
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $errors = $validation->getErrors();
                $errorMsg = implode(', ', $errors);
                return redirect()->back()->withInput()->with('error', $errorMsg);
            }

            $imageFile = $this->request->getFile('image');

            if (!$imageFile->isValid()) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors du téléchargement de l\'image');
            }

            // Upload simplifié vers public/assets/products
            $newName = $imageFile->getRandomName();
            $uploadPath = FCPATH . 'assets/products';

            // Créer le dossier s'il n'existe pas
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $imageFile->move($uploadPath, $newName);
            $imageUrl = base_url('assets/products/' . $newName);

            $titre = $this->request->getPost('titre');
            $description = $this->request->getPost('description');

            $annonceData = [
                'titre' => $titre,
                'description' => $description,
                'prix' => (float) $this->request->getPost('prix'),
                'etat' => $this->request->getPost('etat'),
                'taille' => $this->request->getPost('taille'),
                'taille_systeme' => $this->request->getPost('taille_systeme'),
                'id_couleur' => (int) $this->request->getPost('id_couleur'),
                'id_marque' => (int) $this->request->getPost('id_marque'),
                'id_materiau' => (int) $this->request->getPost('id_materiau'),
                'id_utilisateur_vendeur' => $userId,
                'disponible' => true
            ];

            // Générer l'embedding pour la recherche sémantique
            $texte = $titre . ' ' . $description;
            $embedding = $annoncesModel->generateEmbedding($texte);

            if ($embedding) {
                $annonceData['embeddings'] = $embedding;
            }

            log_message('info', 'Creating annonce with data (with embeddings): ' . json_encode($annonceData));

            $annonce = $annoncesModel->publier($annonceData);

            if (!$annonce) {
                log_message('error', 'Failed to create annonce');
                return redirect()->back()->withInput()->with('error', 'Erreur lors de la création de l\'annonce');
            }

            $imageData = [
                'url' => $imageUrl,
                'est_principale' => true
            ];

            $annoncesModel->ajouterImages($annonce['id_annonce'], $imageData);

            log_message('info', 'Annonce created successfully with ID: ' . $annonce['id_annonce']);

            return redirect()->to(base_url('produit/' . $annonce['id_annonce']))->with('success', 'Votre annonce a été publiée avec succès !');
        } catch (\Exception $e) {
            log_message('error', 'Error in Vendre::publier: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Page de gestion des annonces de l'utilisateur
     *
     * URL: GET /mes-annonces
     *
     * @return string|RedirectResponse
     */
    public function mesAnnonces(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('connexion'))->with('error', 'Vous devez être connecté');
        }

        $userId = session()->get('user_id');
        $annoncesModel = new AnnoncesModel();
        $db = \Config\Database::connect();

        // Pagination
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Compter le total d'annonces
        $countQuery = $db->query(
            "SELECT COUNT(*) as total FROM annonce_list_by_vendeur(?) a",
            [$userId]
        );
        $totalAnnonces = $countQuery->getRowArray()['total'];
        $totalPages = ceil($totalAnnonces / $perPage);

        // Récupérer les annonces
        $annonces = $db->query(
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
             ORDER BY a.date_publication DESC
             LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        )->getResultArray();

        return view('Vente/mes-annonces', [
            'annonces' => $annonces,
            'user' => [
                'id_utilisateur' => $userId,
                'prenom' => session()->get('user_prenom'),
                'nom' => session()->get('user_nom'),
                'type_compte' => session()->get('user_type_compte')
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalAnnonces
            ]
        ]);
    }

    /**
     * Page de modification d'une annonce
     *
     * URL: GET /modifier-annonce/{id}
     *
     * @param string $idAnnonce
     * @return string|RedirectResponse
     */
    public function modifier(string $idAnnonce): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('connexion'))->with('error', 'Vous devez être connecté');
        }

        $userId = session()->get('user_id');
        $annoncesModel = new AnnoncesModel();
        $annonce = $annoncesModel->getAnnonce($idAnnonce);

        if (!$annonce) {
            return redirect()->to(base_url('mes-annonces'))->with('error', 'Annonce introuvable');
        }

        // Vérifier que l'utilisateur est bien le propriétaire
        if ($annonce['id_utilisateur_vendeur'] !== $userId) {
            return redirect()->to(base_url('mes-annonces'))->with('error', 'Vous n\'êtes pas autorisé à modifier cette annonce');
        }

        $couleurModel = new CouleurModel();
        $marqueModel = new MarqueModel();
        $materiauModel = new MateriauModel();

        $couleurs = $couleurModel->listerCouleurs();
        $marques = $marqueModel->listerMarques();
        $materiaux = $materiauModel->listerMateriaux();

        return view('Vente/modifier', [
            'annonce' => $annonce,
            'couleurs' => $couleurs,
            'marques' => $marques,
            'materiaux' => $materiaux
        ]);
    }

    /**
     * Traiter la modification d'une annonce
     *
     * URL: POST /modifier-annonce/{id}
     *
     * @param string $idAnnonce
     * @return RedirectResponse
     */
    public function updateAnnonce(string $idAnnonce): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('connexion'))->with('error', 'Vous devez être connecté');
        }

        $userId = session()->get('user_id');
        $annoncesModel = new AnnoncesModel();
        $annonce = $annoncesModel->getAnnonce($idAnnonce);

        if (!$annonce || $annonce['id_utilisateur_vendeur'] !== $userId) {
            return redirect()->to(base_url('mes-annonces'))->with('error', 'Annonce introuvable ou non autorisée');
        }

        $validation = \Config\Services::validation();

        // Règles de validation avec messages en français
        $validation->setRules([
            'titre' => [
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => 'Le titre est obligatoire',
                    'min_length' => 'Le titre doit contenir au moins 3 caractères',
                    'max_length' => 'Le titre ne peut pas dépasser 200 caractères'
                ]
            ],
            'description' => [
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'La description est obligatoire',
                    'min_length' => 'La description doit contenir au moins 10 caractères'
                ]
            ],
            'prix' => [
                'rules' => 'required|decimal|greater_than[0]',
                'errors' => [
                    'required' => 'Le prix est obligatoire',
                    'decimal' => 'Le prix doit être un nombre valide',
                    'greater_than' => 'Le prix doit être supérieur à 0'
                ]
            ],
            'etat' => [
                'rules' => 'required|in_list[neuf,tres_bon,bon,correct]',
                'errors' => [
                    'required' => 'L\'état est obligatoire',
                    'in_list' => 'L\'état sélectionné n\'est pas valide'
                ]
            ],
            'taille' => [
                'rules' => 'required',
                'errors' => ['required' => 'La taille est obligatoire']
            ],
            'taille_systeme' => [
                'rules' => 'required|in_list[EU,US,UK]',
                'errors' => [
                    'required' => 'Le système de taille est obligatoire',
                    'in_list' => 'Le système de taille n\'est pas valide'
                ]
            ],
            'id_couleur' => [
                'rules' => 'required',
                'errors' => ['required' => 'La couleur est obligatoire']
            ],
            'id_marque' => [
                'rules' => 'required',
                'errors' => ['required' => 'La marque est obligatoire']
            ],
            'id_materiau' => [
                'rules' => 'required',
                'errors' => ['required' => 'Le matériau est obligatoire']
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $errorMsg = implode(', ', $errors);
            return redirect()->back()->withInput()->with('error', $errorMsg);
        }

        $titre = $this->request->getPost('titre');
        $description = $this->request->getPost('description');

        $annonceData = [
            'titre' => $titre,
            'description' => $description,
            'prix' => (float) $this->request->getPost('prix'),
            'etat' => $this->request->getPost('etat'),
            'taille' => $this->request->getPost('taille'),
            'taille_systeme' => $this->request->getPost('taille_systeme'),
            'id_couleur' => (int) $this->request->getPost('id_couleur'),
            'id_marque' => (int) $this->request->getPost('id_marque'),
            'id_materiau' => (int) $this->request->getPost('id_materiau')
        ];

        // Régénérer l'embedding si titre/description a changé
        if ($titre !== $annonce['titre'] || $description !== $annonce['description']) {
            $texte = $titre . ' ' . $description;
            $embedding = $annoncesModel->generateEmbedding($texte);

            if ($embedding) {
                $annonceData['embeddings'] = $embedding;
            }
        }

        // Gérer l'upload d'une nouvelle image si fournie
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid()) {
            $newName = $imageFile->getRandomName();
            $uploadPath = FCPATH . 'assets/products';

            // Créer le dossier s'il n'existe pas
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $imageFile->move($uploadPath, $newName);
            $imageUrl = base_url('assets/products/' . $newName);

            $imageData = [
                'url' => $imageUrl,
                'est_principale' => true
            ];

            $annoncesModel->ajouterImages($idAnnonce, $imageData);
        }

        $updated = $annoncesModel->modifier($idAnnonce, $annonceData);

        if (!$updated) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification de l\'annonce');
        }

        return redirect()->to(base_url('mes-annonces'))->with('success', 'Votre annonce a été modifiée avec succès !');
    }

    /**
     * Supprimer une annonce
     *
     * URL: POST /supprimer-annonce/{id}
     *
     * @param string $idAnnonce
     * @return RedirectResponse
     */
    public function supprimerAnnonce(string $idAnnonce): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('connexion'))->with('error', 'Vous devez être connecté');
        }

        $userId = session()->get('user_id');
        $annoncesModel = new AnnoncesModel();
        $annonce = $annoncesModel->getAnnonce($idAnnonce);

        if (!$annonce || $annonce['id_utilisateur_vendeur'] !== $userId) {
            return redirect()->to(base_url('mes-annonces'))->with('error', 'Annonce introuvable ou non autorisée');
        }

        $success = $annoncesModel->supprimer($idAnnonce);

        if (!$success) {
            return redirect()->to(base_url('mes-annonces'))->with('error', 'Erreur lors de la suppression de l\'annonce');
        }

        return redirect()->to(base_url('mes-annonces'))->with('success', 'Votre annonce a été supprimée avec succès !');
    }
}
