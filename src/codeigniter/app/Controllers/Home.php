<?php

namespace App\Controllers;

use App\Models\AnnoncesModel;
use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    protected $annoncesModel;

    /**
     * Constructeur - Initialise le modèle des annonces
     */
    public function __construct()
    {
        $this->annoncesModel = new AnnoncesModel();
    }

    /**
     * Afficher la page d'accueil
     *
     * Affiche les 20 dernières annonces disponibles
     * Redirige vers /recherche si une recherche est effectuée depuis la navbar
     *
     * URL: /
     * Paramètres GET: q (optionnel) - Requête de recherche
     *
     * @return string|RedirectResponse Vue de la page d'accueil ou redirection vers la recherche
     */
    public function index(): string|RedirectResponse
    {
        // Si une recherche est effectuée depuis la navbar, rediriger vers /recherche
        $searchQuery = $this->request->getGet('q');
        if (!empty($searchQuery)) {
            return redirect()->to(base_url('recherche') . '?q=' . urlencode($searchQuery));
        }

        // Récupérer les 20 dernières annonces via le modèle
        $products = $this->annoncesModel->getAnnoncesDisponibles(20, 0);

        return view('HomePage/page', ['products' => $products]);
    }
}
