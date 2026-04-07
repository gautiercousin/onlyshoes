<?php

namespace App\Services\Search;

use App\Models\AnnoncesModel;

class SemanticSearchStrategy implements SearchStrategy
{
    private AnnoncesModel $annoncesModel;

    /**
     * Stratégie de recherche sémantique
     *
     * @param AnnoncesModel $annoncesModel Modèle des annonces
     */
    public function __construct(AnnoncesModel $annoncesModel)
    {
        $this->annoncesModel = $annoncesModel;
    }

    /**
     * Rechercher via embeddings sémantiques
     *
     * @param SearchCriteria $criteria Critères de recherche
     * @return array Résultats de recherche
     */
    public function search(SearchCriteria $criteria): array
    {
        return $this->annoncesModel->rechercherSemantique(
            $criteria->getQuery(),
            $criteria->getFilters(),
            $criteria->getLimit()
        );
    }
}
