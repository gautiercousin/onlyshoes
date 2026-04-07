<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AnnoncesModel - Modèle pour la gestion des annonces (produits)
 *
 * Cette classe gère toutes les opérations liées aux annonces en utilisant
 * les procédures stockées PostgreSQL pour la logique métier.
 */
class AnnoncesModel extends Model
{
    protected $table = 'annonce';
    protected $primaryKey = 'id_annonce';
    protected $returnType = 'array';
    protected $allowedFields = [
        'titre', 'description', 'prix', 'etat', 'taille_systeme', 'taille',
        'disponible', 'embeddings', 'id_couleur', 'id_materiau', 'id_marque',
        'id_image', 'id_utilisateur_vendeur'
    ];

    /**
     * Récupérer une annonce par son ID avec métadonnées
     */
    public function getAnnonce(string $idAnnonce): ?array
    {
        $query = $this->db->query(
            "SELECT 
                a.*,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom,
                i.url as image_url,
                i.est_principale as image_principale
             FROM annonce_read(?) a
             LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
             LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
             LEFT JOIN marque br ON a.id_marque = br.id_marque
             LEFT JOIN image i ON a.id_image = i.id_image
             WHERE a.disponible = true",
            [$idAnnonce]
        );

        return $query->getRowArray();
    }

    /**
     * Récupérer les produits similaires via procédure stockée annonce_find_similar()
     */
    public function getSimilarProducts(string $idAnnonceCourante, int $limit = 4): array
    {
        $query = $this->db->query(
            "SELECT 
                a.*,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom,
                i.url as image_url,
                i.est_principale as image_principale,
                s.similarity_score
             FROM annonce_find_similar(?, ?) s
             JOIN annonce a ON a.id_annonce = s.id_annonce
             LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
             LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
             LEFT JOIN marque br ON a.id_marque = br.id_marque
             LEFT JOIN image i ON a.id_image = i.id_image
             ORDER BY s.similarity_score DESC",
            [$idAnnonceCourante, $limit]
        );

        return $query->getResultArray();
    }

    /**
     * Publier une nouvelle annonce
     *
     * Crée une nouvelle annonce via la procédure stockée annonce_create()
     *
     * @param array $data Données de l'annonce (titre, description, prix, id_utilisateur_vendeur, etc.)
     * @return array|null L'annonce créée avec son ID, ou null en cas d'erreur
     */
    public function publier(array $data): ?array
    {
        if (empty($data['id_utilisateur_vendeur'])) {
            return null;
        }

        $idVendeur = $data['id_utilisateur_vendeur'];
        $payload = $data;
        unset($payload['id_utilisateur_vendeur']);

        try {
            $sql = "SELECT * FROM annonce_create(?, ?::jsonb)";
            $result = $this->db->query($sql, [$idVendeur, json_encode($payload)]);
            return $result->getRowArray() ?: null;
        } catch (\Exception $e) {
            log_message('error', 'Erreur publication annonce: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Modifier une annonce existante
     *
     * Met à jour une annonce via la procédure stockée annonce_update()
     *
     * @param string $idAnnonce ID (UUID) de l'annonce à modifier
     * @param array $data Nouvelles données (titre, description, prix, etc.)
     * @return array|null L'annonce modifiée, ou null en cas d'erreur
     */
    public function modifier(string $idAnnonce, array $data): ?array
    {
        try {
            $sql = "SELECT * FROM annonce_update(?, ?::jsonb)";
            $result = $this->db->query($sql, [$idAnnonce, json_encode($data)]);
            return $result->getRowArray() ?: null;
        } catch (\Exception $e) {
            log_message('error', 'Erreur modification annonce: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Marquer une annonce comme indisponible
     *
     * Utilisé après un achat pour retirer l'annonce de la vente
     * Via la procédure stockée annonce_marquer_indisponible()
     *
     * @param string $idAnnonce ID (UUID) de l'annonce
     * @return bool True si succès, false sinon
     */
    public function marquerIndisponible(string $idAnnonce): bool
    {
        try {
            $sql = "SELECT annonce_marquer_indisponible(?) AS success";
            $result = $this->db->query($sql, [$idAnnonce]);
            $row = $result->getRowArray();
            return isset($row['success']) ? (bool) $row['success'] : false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur indisponible annonce: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une annonce
     *
     * Suppression définitive via la procédure stockée annonce_delete()
     *
     * @param string $idAnnonce ID (UUID) de l'annonce à supprimer
     * @return bool True si succès, false sinon
     */
    public function supprimer(string $idAnnonce): bool
    {
        try {
            $sql = "SELECT annonce_delete(?) AS success";
            $result = $this->db->query($sql, [$idAnnonce]);
            $row = $result->getRowArray();
            return isset($row['success']) ? (bool) $row['success'] : false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression annonce: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer toutes les annonces disponibles avec pagination
     *
     * Utilisé pour afficher les dernières annonces sur la page d'accueil
     * Retourne les annonces avec leurs métadonnées (couleur, marque, matériau, image)
     *
     * @param int $perPage Nombre d'annonces par page (défaut: 20)
     * @param int $offset Décalage pour la pagination (défaut: 0)
     * @return array Liste des annonces disponibles
     */
    public function getAnnoncesDisponibles(int $perPage = 20, int $offset = 0): array
    {
        $query = $this->db->query(
            "SELECT 
                a.*,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom,
                i.url as image_url,
                i.est_principale as image_principale
             FROM annonce_list() a
             LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
             LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
             LEFT JOIN marque br ON a.id_marque = br.id_marque
             LEFT JOIN image i ON a.id_image = i.id_image
             ORDER BY a.date_publication DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        return $query->getResultArray();
    }

    /**
     * Compter le nombre total d'annonces disponibles
     * TODO: Implémenter si utilisation en pagination avancée
     */
    public function countAnnoncesDisponibles(): int
    {
        // TODO: Utiliser si besoin pagination
        $result = $this->db->query(
            "SELECT COUNT(*) as total FROM annonce_list()"
        );

        return (int) $result->getRowArray()['total'];
    }

    /**
     * Recherche sémantique avec embeddings
     * Utilise le service Flask pour générer l'embedding et cherche via similarité vectorielle
     *
     * @param string $query Requête de recherche
     * @param array $filters Filtres optionnels (id_marque, id_couleur, etat)
     * @param int $limit Nombre de résultats max
     * @return array Résultats triés par pertinence sémantique
     */
    public function rechercherSemantique(string $query, array $filters = [], int $limit = 20): array
    {
        // Générer l'embedding pour la requête
        $embeddingString = $this->generateEmbedding($query);

        if (!$embeddingString) {
            // Si échec de génération d'embedding, fallback sur recherche basique
            log_message('warning', 'Semantic search failed, falling back to basic search');
            return $this->rechercherAvecFiltres(array_merge($filters, ['search' => $query]), $limit);
        }

        // Préparer les filtres pour la procédure stockée
        $jsonFilters = !empty($filters) ? json_encode($filters) : null;

        // Appeler la procédure stockée de recherche sémantique
        $queryResult = $this->db->query(
            "SELECT
                a.*,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom,
                i.url as image_url,
                i.est_principale as image_principale,
                a.similarity_score
             FROM annonce_search_by_embedding(?::vector, ?, ?::jsonb) a
             LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
             LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
             LEFT JOIN marque br ON a.id_marque = br.id_marque
             LEFT JOIN image i ON a.id_image = i.id_image
             ORDER BY a.similarity_score DESC",
            [$embeddingString, $limit, $jsonFilters]
        );

        return $queryResult->getResultArray();
    }

    /**
     * Recherche basique avec filtres (sans sémantique)
     * Utilise annonce_list() avec filtres
     *
     * @param array $filters Filtres (id_marque, id_couleur, etat, search)
     * @param int $limit Nombre de résultats max
     * @return array Résultats triés par date
     */
    public function rechercherAvecFiltres(array $filters = [], int $limit = 20): array
    {
        $query = $this->db->query(
            "SELECT
                a.*,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom,
                i.url as image_url,
                i.est_principale as image_principale
             FROM annonce_list(?::jsonb) a
             LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
             LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
             LEFT JOIN marque br ON a.id_marque = br.id_marque
             LEFT JOIN image i ON a.id_image = i.id_image
             ORDER BY a.date_publication DESC
             LIMIT ?",
            [json_encode($filters), $limit]
        );

        return $query->getResultArray();
    }

    /**
     * Rechercher des annonces par terme (DEPRECATED - utiliser rechercherSemantique ou rechercherAvecFiltres)
     * UTILISÉ DANS: Search::index()
     */
    public function rechercher(string $term, int $perPage = 20, int $offset = 0): array
    {
        $filters = ['search' => $term];
        $query = $this->db->query(
            "SELECT
                a.*,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom,
                i.url as image_url,
                i.est_principale as image_principale
             FROM annonce_list(?::jsonb) a
             LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
             LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
             LEFT JOIN marque br ON a.id_marque = br.id_marque
             LEFT JOIN image i ON a.id_image = i.id_image
             ORDER BY a.date_publication DESC
             LIMIT ? OFFSET ?",
            [json_encode($filters), $perPage, $offset]
        );

        return $query->getResultArray();
    }

    /**
     * Compter les résultats de recherche
     * TODO: Implémenter si utilisation en pagination avancée
     */
    public function countRecherche(string $term): int
    {
        // TODO: Utiliser si besoin pagination pour recherche
        $filters = ['search' => $term];
        $result = $this->db->query(
            "SELECT COUNT(*) as total FROM annonce_list(?::jsonb)",
            [json_encode($filters)]
        );

        return (int) $result->getRowArray()['total'];
    }

    /**
     * Ajouter une image à une annonce
     * TODO: Implémenter via procédure stockée image_create() et association
     */
    public function ajouterImages(string $idAnnonce, array $imageData): ?array
    {
        try {
            $sql = "SELECT * FROM image_create(?, ?::jsonb)";
            $result = $this->db->query($sql, [$idAnnonce, json_encode($imageData)]);
            $image = $result->getRowArray();

            if (!$image) {
                return null;
            }

            $updated = $this->db->query(
                "UPDATE annonce SET id_image = ? WHERE id_annonce = ?",
                [$image['id_image'], $idAnnonce]
            );

            if ($updated === false) {
                return null;
            }

            return $image;
        } catch (\Exception $e) {
            log_message('error', 'Erreur ajout image annonce: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer une image d'une annonce
     * TODO: Implémenter via procédure stockée image_delete()
     */
    public function supprimerImage(string $idImage): bool
    {
        try {
            $sql = "SELECT image_delete(?) AS success";
            $result = $this->db->query($sql, [(int) $idImage]);
            $row = $result->getRowArray();
            return isset($row['success']) ? (bool) $row['success'] : false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Générer un embedding pour un texte via le service Flask
     * Helper interne pour publier() et modifier()
     */
    public function generateEmbedding(string $text): ?string
    {
        try {
            $client = \Config\Services::curlrequest();
            $response = $client->post('http://embeddings:5000/create_embedding_text', [
                'json' => ['text' => $text],
                'timeout' => 30
            ]);

            if ($response->getStatusCode() !== 200) {
                log_message('error', 'Embeddings service error: ' . $response->getBody());
                return null;
            }

            $data = json_decode($response->getBody(), true);
            if (!isset($data['embedding']) || !is_array($data['embedding'])) {
                log_message('error', 'Invalid embedding response');
                return null;
            }

            $embeddingArray = $data['embedding'];
            $embeddingString = '[' . implode(',', $embeddingArray) . ']';

            return $embeddingString;
        } catch (\Exception $e) {
            log_message('error', 'Embeddings API call failed: ' . $e->getMessage());
            return null;
        }
    }
}
