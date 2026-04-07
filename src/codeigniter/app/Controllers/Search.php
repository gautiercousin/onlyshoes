<?php

namespace App\Controllers;

use App\Models\AnnoncesModel;
use App\Models\CouleurModel;
use App\Models\MarqueModel;
use App\Models\MateriauModel;
use App\Services\Search\SearchCriteria;
use App\Services\Search\SearchStrategyContext;

class Search extends BaseController
{
    protected $annoncesModel;
    protected $searchContext;
    protected $marqueModel;
    protected $couleurModel;
    protected $materiauModel;

    /**
     * Constructeur - Initialise les modèles et le contexte de recherche
     *
     * Initialise :
     * - AnnoncesModel pour l'accès aux produits
     * - SearchStrategyContext pour le pattern Strategy (recherche sémantique/filtrée)
     * - MarqueModel, CouleurModel, MateriauModel via pattern DAO pour les filtres
     */
    public function __construct()
    {
        $this->annoncesModel = new AnnoncesModel();
        $this->searchContext = new SearchStrategyContext($this->annoncesModel);
        $this->marqueModel = new MarqueModel();
        $this->couleurModel = new CouleurModel();
        $this->materiauModel = new MateriauModel();
    }

    /**
     * Afficher la page de recherche avec résultats
     *
     * URL: /recherche
     * Exemple: /recherche?q=air%20max&marque=1&couleur=2&etat=neuf
     *
     * @return string
     */
    public function index(): string
    {
        // Récupérer les paramètres de recherche
        $query = $this->request->getGet('q') ?? '';
        $idMarque = $this->request->getGet('marque') ?? '';
        $idCouleur = $this->request->getGet('couleur') ?? '';
        $idMateriau = $this->request->getGet('materiau') ?? '';
        $etat = $this->request->getGet('etat') ?? '';
        $tailleSysteme = $this->request->getGet('taille_systeme') ?? '';
        $taille = $this->request->getGet('taille') ?? '';
        $prixMin = $this->request->getGet('prix_min') ?? '';
        $prixMax = $this->request->getGet('prix_max') ?? '';

        // Pagination
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = max(1, $page); // S'assurer que page >= 1

        // Récupérer les listes pour les filtres via le DAO
        $marques = $this->marqueModel->listerMarques();
        $couleurs = $this->couleurModel->listerCouleurs();
        $materiaux = $this->materiauModel->listerMateriaux();

        // États disponibles (définis dans la contrainte CHECK de la table)
        $etats = [
            ['value' => 'neuf', 'label' => 'Neuf'],
            ['value' => 'comme_neuf', 'label' => 'Comme neuf'],
            ['value' => 'tres_bon', 'label' => 'Très bon état'],
            ['value' => 'bon', 'label' => 'Bon état'],
            ['value' => 'correct', 'label' => 'État correct']
        ];

        // Systèmes de taille disponibles
        $tailleSystemes = [
            ['value' => 'EU', 'label' => 'EU (Europe)'],
            ['value' => 'US', 'label' => 'US (États-Unis)'],
            ['value' => 'UK', 'label' => 'UK (Royaume-Uni)']
        ];

        // Construire les filtres pour la recherche
        $filters = [];
        if (!empty($idMarque)) {
            $filters['id_marque'] = (int)$idMarque;
        }
        if (!empty($idCouleur)) {
            $filters['id_couleur'] = (int)$idCouleur;
        }
        if (!empty($idMateriau)) {
            $filters['id_materiau'] = (int)$idMateriau;
        }
        if (!empty($etat)) {
            $filters['etat'] = $etat;
        }
        if (!empty($tailleSysteme)) {
            $filters['taille_systeme'] = $tailleSysteme;
        }
        if (!empty($taille)) {
            $filters['taille'] = $taille;
        }
        if (!empty($prixMin)) {
            $filters['prix_min'] = (float)$prixMin;
        }
        if (!empty($prixMax)) {
            $filters['prix_max'] = (float)$prixMax;
        }

        // Rechercher tous les résultats (limite haute pour compter)
        $criteria = new SearchCriteria($query, $filters, 1000);
        $allResults = $this->searchContext->search($criteria);

        // Compter le total
        $totalItems = count($allResults);
        $totalPages = ceil($totalItems / $perPage);

        // Paginer les résultats en PHP
        $offset = ($page - 1) * $perPage;
        $results = array_slice($allResults, $offset, $perPage);

        return view('Search/page', [
            'query' => $query,
            'marques' => $marques,
            'couleurs' => $couleurs,
            'materiaux' => $materiaux,
            'etats' => $etats,
            'taille_systemes' => $tailleSystemes,
            'marque_selectionnee' => $idMarque,
            'couleur_selectionnee' => $idCouleur,
            'materiau_selectionne' => $idMateriau,
            'etat_selectionne' => $etat,
            'taille_systeme_selectionne' => $tailleSysteme,
            'taille_selectionnee' => $taille,
            'prix_min' => $prixMin,
            'prix_max' => $prixMax,
            'results' => $results,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalItems
            ]
        ]);
    }
}
