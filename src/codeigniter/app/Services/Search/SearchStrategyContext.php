<?php

namespace App\Services\Search;

use App\Models\AnnoncesModel;

class SearchStrategyContext
{
    private SearchStrategy $semanticStrategy;
    private SearchStrategy $filteredStrategy;
    private SearchStrategy $listAllStrategy;

    /**
     * Contexte de sélection des stratégies de recherche
     *
     * @param AnnoncesModel $annoncesModel Modèle des annonces
     */
    public function __construct(AnnoncesModel $annoncesModel)
    {
        $this->semanticStrategy = new SemanticSearchStrategy($annoncesModel);
        $this->filteredStrategy = new FilteredSearchStrategy($annoncesModel);
        $this->listAllStrategy = new ListAllSearchStrategy($annoncesModel);
    }

    /**
     * Lancer la recherche via la stratégie appropriée
     *
     * @param SearchCriteria $criteria Critères de recherche
     * @return array Résultats de recherche
     */
    public function search(SearchCriteria $criteria): array
    {
        return $this->selectStrategy($criteria)->search($criteria);
    }

    /**
     * Sélectionner la stratégie à appliquer
     *
     * @param SearchCriteria $criteria Critères de recherche
     * @return SearchStrategy
     */
    private function selectStrategy(SearchCriteria $criteria): SearchStrategy
    {
        if ($criteria->hasQuery()) {
            return $this->semanticStrategy;
        }

        if ($criteria->hasFilters()) {
            return $this->filteredStrategy;
        }

        return $this->listAllStrategy;
    }
}
