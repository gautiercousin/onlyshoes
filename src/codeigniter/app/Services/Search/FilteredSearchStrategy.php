<?php

namespace App\Services\Search;

use App\Models\AnnoncesModel;

class FilteredSearchStrategy implements SearchStrategy
{
    private AnnoncesModel $annoncesModel;

    /**
     * Stratégie de recherche filtrée (sans sémantique)
     *
     * @param AnnoncesModel $annoncesModel Modèle des annonces
     */
    public function __construct(AnnoncesModel $annoncesModel)
    {
        $this->annoncesModel = $annoncesModel;
    }

    /**
     * Rechercher avec filtres simples
     *
     * @param SearchCriteria $criteria Critères de recherche
     * @return array Résultats de recherche
     */
    public function search(SearchCriteria $criteria): array
    {
        return $this->annoncesModel->rechercherAvecFiltres(
            $criteria->getFilters(),
            $criteria->getLimit()
        );
    }
}
