<?php

namespace App\Services\Search;

use App\Models\AnnoncesModel;

class ListAllSearchStrategy implements SearchStrategy
{
    private AnnoncesModel $annoncesModel;

    /**
     * Stratégie de listing complet (sans requête ni filtre)
     *
     * @param AnnoncesModel $annoncesModel Modèle des annonces
     */
    public function __construct(AnnoncesModel $annoncesModel)
    {
        $this->annoncesModel = $annoncesModel;
    }

    /**
     * Lister les annonces disponibles
     *
     * @param SearchCriteria $criteria Critères de recherche
     * @return array Résultats de recherche
     */
    public function search(SearchCriteria $criteria): array
    {
        return $this->annoncesModel->getAnnoncesDisponibles(
            $criteria->getLimit(),
            0
        );
    }
}
