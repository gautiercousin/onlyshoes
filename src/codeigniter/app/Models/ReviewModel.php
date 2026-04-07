<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table = 'REVIEW';
    protected $primaryKey = 'id_review';
    protected $allowedFields = ['note', 'commentaire', 'id_utilisateur_auteur', 'id_utilisateur_vendeur'];

    /**
     * Créer un avis
     *
     * Utilise la procédure stockée review_create() qui valide :
     * - L'acheteur doit avoir acheté au moins un produit du vendeur (SA024)
     * - Un seul avis autorisé par couple acheteur-vendeur (SA023)
     *
     * @param string $idAuteur UUID de l'acheteur
     * @param string $idVendeur UUID du vendeur
     * @param array $data Données de l'avis (note, commentaire)
     * @return array|null L'avis créé ou null si erreur
     */
    public function creerAvis(string $idAuteur, string $idVendeur, array $data): ?array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query(
                "SELECT * FROM review_create(?::uuid, ?::uuid, ?::jsonb)",
                [
                    $idAuteur,
                    $idVendeur,
                    json_encode($data)
                ]
            );

            return $query->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur création avis: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lire un avis par son ID
     *
     * @param string $idReview UUID de l'avis
     * @return array|null L'avis ou null si introuvable
     */
    public function getAvis(string $idReview): ?array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query("SELECT * FROM review_read(?::uuid)", [$idReview]);
            return $query->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur lecture avis: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Modifier un avis existant
     *
     * @param string $idReview UUID de l'avis
     * @param array $data Nouvelles données (note, commentaire)
     * @return array|null L'avis modifié ou null si erreur
     */
    public function modifierAvis(string $idReview, array $data): ?array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query(
                "SELECT * FROM review_update(?::uuid, ?::jsonb)",
                [$idReview, json_encode($data)]
            );

            return $query->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur modification avis: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer un avis
     *
     * @param string $idReview UUID de l'avis
     * @return bool True si succès, false sinon
     */
    public function supprimerAvis(string $idReview): bool
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query("SELECT review_delete(?::uuid)", [$idReview]);
            $result = $query->getRowArray();
            return $result['review_delete'] ?? false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression avis: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lister les avis d'un vendeur
     *
     * @param string $idVendeur UUID du vendeur
     * @return array Liste des avis avec détails auteur
     */
    public function getAvisVendeur(string $idVendeur): array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query("SELECT * FROM review_list_by_vendeur(?::uuid)", [$idVendeur]);
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur liste avis vendeur: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lister les avis d'un vendeur avec pagination
     *
     * @param string $idVendeur UUID du vendeur
     * @param int $limit Nombre d'avis par page
     * @param int $offset Offset pour la pagination
     * @param string|null $excludeReviewId UUID de l'avis à exclure (optionnel)
     * @return array Liste paginée des avis avec détails auteur
     */
    public function getAvisVendeurPaginated(string $idVendeur, int $limit, int $offset, ?string $excludeReviewId = null): array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query(
                "SELECT * FROM review_list_by_vendeur(?::uuid, ?, ?, ?::uuid)",
                [$idVendeur, $limit, $offset, $excludeReviewId]
            );
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur liste avis vendeur paginée: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Compter le nombre total d'avis d'un vendeur
     *
     * @param string $idVendeur UUID du vendeur
     * @param string|null $excludeReviewId UUID de l'avis à exclure du comptage (optionnel)
     * @return int Nombre total d'avis
     */
    public function countAvisVendeur(string $idVendeur, ?string $excludeReviewId = null): int
    {
        $db = \Config\Database::connect();

        try {
            if ($excludeReviewId) {
                $query = $db->query(
                    "SELECT COUNT(*) as total FROM REVIEW WHERE id_utilisateur_vendeur = ?::uuid AND id_review != ?::uuid",
                    [$idVendeur, $excludeReviewId]
                );
            } else {
                $query = $db->query(
                    "SELECT COUNT(*) as total FROM REVIEW WHERE id_utilisateur_vendeur = ?::uuid",
                    [$idVendeur]
                );
            }
            $result = $query->getRowArray();
            return (int) ($result['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'Erreur comptage avis vendeur: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Vérifier si un avis existe déjà entre un acheteur et un vendeur
     *
     * @param string $idAuteur UUID de l'acheteur
     * @param string $idVendeur UUID du vendeur
     * @return array|null L'avis existant ou null
     */
    public function getAvisExistant(string $idAuteur, string $idVendeur): ?array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query(
                "SELECT * FROM REVIEW
                 WHERE id_utilisateur_auteur = ?::uuid
                 AND id_utilisateur_vendeur = ?::uuid",
                [$idAuteur, $idVendeur]
            );

            return $query->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur vérification avis existant: ' . $e->getMessage());
            return null;
        }
    }
}
