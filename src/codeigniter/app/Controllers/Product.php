<?php

namespace App\Controllers;

use App\Models\AnnoncesModel;
use App\Models\UtilisateurModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Product extends BaseController
{
    protected $annoncesModel;
    protected $utilisateurModel;

    /**
     * Constructeur - Initialise les modèles nécessaires
     */
    public function __construct()
    {
        $this->annoncesModel = new AnnoncesModel();
        $this->utilisateurModel = new UtilisateurModel();
    }

    /**
     * Afficher la page détaillée d'un produit
     *
     * Affiche les informations complètes d'une annonce avec :
     * - Les détails du produit (titre, description, prix, etc.)
     * - Les informations du vendeur
     * - 4 produits similaires (basés sur les embeddings)
     *
     * URL: /produit/{id}
     *
     * @param string $id ID (UUID) de l'annonce
     * @return string Vue de la page produit
     * @throws PageNotFoundException Si le produit n'existe pas ou n'est pas disponible
     */
    public function show($id): string
    {
        // Récupérer le produit via le modèle
        $product = $this->annoncesModel->getAnnonce($id);

        if (!$product) {
            throw PageNotFoundException::forPageNotFound("Produit $id introuvable");
        }

        // Récupérer le vendeur via le modèle
        $seller = $this->utilisateurModel->getUtilisateur($product['id_utilisateur_vendeur']);

        // Récupérer les produits similaires via le modèle
        $similarProducts = $this->annoncesModel->getSimilarProducts($id, 4);

        return view('Product/page', [
            'product' => $product,
            'seller' => $seller,
            'similarProducts' => $similarProducts,
        ]);
    }
}
