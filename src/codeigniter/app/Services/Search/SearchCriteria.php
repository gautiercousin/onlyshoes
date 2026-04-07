<?php

namespace App\Services\Search;

class SearchCriteria
{
    private string $query;
    private array $filters;
    private int $limit;

    /**
     * Construire un ensemble de critères de recherche
     *
     * @param string $query Texte de recherche
     * @param array $filters Filtres appliqués (marque, couleur, etat, etc.)
     * @param int $limit Nombre maximum de résultats
     */
    public function __construct(string $query, array $filters, int $limit)
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->limit = $limit;
    }

    /**
     * Obtenir le texte de recherche
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Obtenir les filtres de recherche
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Obtenir la limite de résultats
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Indiquer si une requête texte est définie
     *
     * @return bool
     */
    public function hasQuery(): bool
    {
        return $this->query !== '';
    }

    /**
     * Indiquer si des filtres sont définis
     *
     * @return bool
     */
    public function hasFilters(): bool
    {
        return !empty($this->filters);
    }
}
