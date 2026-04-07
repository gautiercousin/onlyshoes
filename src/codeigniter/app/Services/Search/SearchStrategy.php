<?php

namespace App\Services\Search;

interface SearchStrategy
{
    /**
     * Exécuter une recherche à partir des critères
     *
     * @param SearchCriteria $criteria Critères de recherche
     * @return array Résultats de recherche
     */
    public function search(SearchCriteria $criteria): array;
}
