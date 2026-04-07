<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AdminModel - Modèle pour les statistiques et opérations d'administration
 */
class AdminModel extends Model
{
    /**
     * Obtenir le nombre total de comptes utilisateurs
     */
    public function getTotalComptes(): int
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as total FROM utilisateur_list()");
        $result = $query->getRow();
        return (int) $result->total;
    }

    /**
     * Obtenir le nombre total de produits (annonces)
     */
    public function getTotalProduits(): int
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as total FROM annonce");
        $result = $query->getRow();
        return (int) $result->total;
    }

    /**
     * Obtenir le nombre de signalements en attente
     */
    public function getSignalementsEnAttente(): int
    {
        $db = \Config\Database::connect();
        $filters = json_encode(['statut' => 'en_attente']);
        $query = $db->query("SELECT COUNT(*) as total FROM signalement_list(?::jsonb)", [$filters]);
        $result = $query->getRow();
        return (int) $result->total;
    }

    /**
     * Obtenir tous les signalements avec détails (annonces, reviews, comptes)
     * 
     * @param string $statut Filtrer par statut ('en_attente', 'traite', 'rejete', 'tous')
     * @param string $type Filtrer par type ('annonce', 'user', 'review', 'tous')
     * @param int $page Page actuelle (défaut: 1)
     * @param int $perPage Nombre d'éléments par page (défaut: 20)
     * @return array Tableau avec 'signalements' et 'pagination'
     */
    public function getAllSignalements(string $statut = 'en_attente', string $type = 'tous', int $page = 1, int $perPage = 20): array
    {
        $db = \Config\Database::connect();
        
        // Construire la clause WHERE dynamiquement
        $whereConditions = [];
        
        // Filtre par statut
        if ($statut !== 'tous') {
            $whereConditions[] = "s.statut = " . $db->escape($statut);
        }
        
        // Filtre par type
        if ($type !== 'tous') {
            $whereConditions[] = "s.type = " . $db->escape($type);
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Compter le total d'éléments
        $countQuery = $db->query("
            SELECT COUNT(*) as total
            FROM signalement s
            INNER JOIN utilisateur u_auteur ON s.id_utilisateur_auteur = u_auteur.id_utilisateur
            LEFT JOIN utilisateur u_cible ON s.id_utilisateur_cible = u_cible.id_utilisateur
            LEFT JOIN annonce a ON s.id_annonce_cible = a.id_annonce
            $whereClause
        ");
        $total = $countQuery->getRow()->total ?? 0;
        
        // Calculer la pagination
        $totalPages = (int) ceil($total / $perPage);
        $page = max(1, min($page, max(1, $totalPages)));
        $offset = ($page - 1) * $perPage;
        
        $query = $db->query("
            SELECT 
                s.id_signalement,
                s.motif,
                s.description,
                s.date,
                s.statut,
                s.type,
                COALESCE(s.id_review_cible, s.id_utilisateur_cible, s.id_annonce_cible) as id_cible,
                s.raison_decision,
                s.date_traitement,
                u_auteur.nom as auteur_nom,
                u_auteur.prenom as auteur_prenom,
                u_auteur.email as auteur_email,
                u_auteur.id_utilisateur as auteur_id,
                CASE 
                    WHEN s.type = 'annonce' THEN a.titre
                    WHEN s.type = 'user' THEN CONCAT(u_cible.prenom, ' ', u_cible.nom)
                    WHEN s.type = 'review' THEN CONCAT('Review #', CAST(s.id_review_cible AS TEXT))
                END as cible_nom,
                CASE
                    WHEN s.type = 'annonce' THEN a.id_annonce::text
                    WHEN s.type = 'user' THEN u_cible.id_utilisateur::text
                    WHEN s.type = 'review' THEN s.id_review_cible::text
                END as cible_id_display
            FROM signalement s
            INNER JOIN utilisateur u_auteur ON s.id_utilisateur_auteur = u_auteur.id_utilisateur
            LEFT JOIN utilisateur u_cible ON s.id_utilisateur_cible = u_cible.id_utilisateur
            LEFT JOIN annonce a ON s.id_annonce_cible = a.id_annonce
            $whereClause
            ORDER BY s.date DESC
            LIMIT $perPage OFFSET $offset
        ");
        
        return [
            'signalements' => $query->getResultArray(),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $total
            ]
        ];
    }

    /**
     * Obtenir la liste de tous les utilisateurs
     */
    public function getAllUtilisateurs(): array
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM utilisateur_list()");
        $rows = $query->getResultArray();
        $utilisateurs = [];

        foreach ($rows as $row) {
            $utilisateurs[] = [
                'id_utilisateur' => $row['id_utilisateur'],
                'nom' => $row['nom'],
                'prenom' => $row['prenom'],
                'email' => $row['email'],
                'type_compte' => $row['type_compte'],
                'date_creation' => $row['date_creation'],
            ];
        }
        
        return $utilisateurs;
    }

    /**
     * Obtenir la liste de toutes les annonces
     */
    public function getAllAnnonces(): array
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                a.id_annonce,
                a.titre,
                a.prix,
                a.disponible,
                a.date_publication,
                u.nom as vendeur_nom,
                u.prenom as vendeur_prenom
            FROM annonce a
            INNER JOIN utilisateur u ON a.id_utilisateur_vendeur = u.id_utilisateur
            ORDER BY a.date_publication DESC
        ");
        
        return $query->getResultArray();
    }

    /**
     * Obtenir les annonces signalées avec détails (Exigence 32)
     */
    public function getAnnoncesSignalees(): array
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                a.id_annonce,
                a.titre,
                a.description,
                a.prix,
                a.date_publication,
                u.nom as vendeur_nom,
                u.prenom as vendeur_prenom,
                u.email as vendeur_email,
                u.id_utilisateur as id_vendeur,
                i.url as image_url,
                COUNT(DISTINCT s.id_signalement) as nombre_signalements,
                MAX(s.date) as dernier_signalement
            FROM annonce a
            INNER JOIN utilisateur u ON a.id_utilisateur_vendeur = u.id_utilisateur
            LEFT JOIN image i ON a.id_image = i.id_image
            INNER JOIN signalement s ON s.id_annonce_cible = a.id_annonce
            WHERE s.statut = 'en_attente'
            GROUP BY a.id_annonce, u.id_utilisateur, i.url
            ORDER BY nombre_signalements DESC, dernier_signalement DESC
        ");
        
        return $query->getResultArray();
    }

    /**
     * Obtenir le nombre d'annonces signalées (Exigence 32)
     */
    public function getAnnoncesSignaleesCount(): int
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(DISTINCT a.id_annonce) as total
            FROM annonce a
            INNER JOIN signalement s ON s.id_annonce_cible = a.id_annonce
            WHERE s.statut = 'en_attente'
        ");
        $result = $query->getRow();
        return (int) $result->total;
    }

    /**
     * Obtenir les détails d'une annonce signalée avec tous les signalements
     */
    public function getAnnonceSignaleeDetails(string $idAnnonce): array
    {
        $db = \Config\Database::connect();
        
        // Récupérer les informations de l'annonce
        $queryAnnonce = $db->query("
            SELECT 
                a.*,
                u.nom as vendeur_nom,
                u.prenom as vendeur_prenom,
                u.email as vendeur_email,
                u.id_utilisateur as id_vendeur,
                i.url as image_url,
                c.nom as couleur_nom,
                m.nom as materiau_nom,
                br.nom as marque_nom
            FROM annonce a
            INNER JOIN utilisateur u ON a.id_utilisateur_vendeur = u.id_utilisateur
            LEFT JOIN image i ON a.id_image = i.id_image
            LEFT JOIN couleur c ON a.id_couleur = c.id_couleur
            LEFT JOIN materiau m ON a.id_materiau = m.id_materiau
            LEFT JOIN marque br ON a.id_marque = br.id_marque
            WHERE a.id_annonce = ?
        ", [$idAnnonce]);
        
        $annonce = $queryAnnonce->getRowArray();
        if (!$annonce) {
            return [];
        }
        
        // Récupérer les signalements pour cette annonce
        $querySignalements = $db->query("
            SELECT 
                s.id_signalement,
                s.motif,
                s.description,
                s.date,
                s.statut,
                u.nom as auteur_nom,
                u.prenom as auteur_prenom,
                u.email as auteur_email
            FROM signalement s
            INNER JOIN utilisateur u ON s.id_utilisateur_auteur = u.id_utilisateur
            WHERE s.id_annonce_cible = ?
            ORDER BY s.date DESC
        ", [$idAnnonce]);
        
        $annonce['signalements'] = $querySignalements->getResultArray();
        
        return $annonce;
    }

    /**
     * Obtenir les détails d'un utilisateur signalé avec tous les signalements
     */
    public function getUtilisateurSignaleDetails(string $idUtilisateur): array
    {
        $db = \Config\Database::connect();
        
        // Récupérer les informations de l'utilisateur
        $queryUser = $db->query("
            SELECT 
                u.*
            FROM utilisateur u
            WHERE u.id_utilisateur = ?
        ", [$idUtilisateur]);
        
        $utilisateur = $queryUser->getRowArray();
        if (!$utilisateur) {
            return [];
        }
        
        // Retirer le mot de passe
        unset($utilisateur['mdp']);
        
        // Récupérer les signalements pour cet utilisateur
        $querySignalements = $db->query("
            SELECT 
                s.id_signalement,
                s.motif,
                s.description,
                s.date,
                s.statut,
                s.raison_decision,
                s.date_traitement,
                u_auteur.nom as auteur_nom,
                u_auteur.prenom as auteur_prenom,
                u_auteur.email as auteur_email
            FROM signalement s
            INNER JOIN utilisateur u_auteur ON s.id_utilisateur_auteur = u_auteur.id_utilisateur
            WHERE s.id_utilisateur_cible = ?
            ORDER BY s.date DESC
        ", [$idUtilisateur]);
        
        $utilisateur['signalements'] = $querySignalements->getResultArray();
        
        // Récupérer les annonces de l'utilisateur
        $queryAnnonces = $db->query("
            SELECT 
                a.id_annonce,
                a.titre,
                a.prix,
                a.disponible,
                a.date_publication
            FROM annonce a
            WHERE a.id_utilisateur_vendeur = ?
            ORDER BY a.date_publication DESC
            LIMIT 10
        ", [$idUtilisateur]);
        
        $utilisateur['annonces'] = $queryAnnonces->getResultArray();
        
        return $utilisateur;
    }

    /**
     * Obtenir les détails d'une review signalée avec tous les signalements
     * 
     * @param string $idReview UUID de la review
     * @return array Détails de la review avec auteur, vendeur et signalements
     */
    public function getReviewSignaleeDetails(string $idReview): array
    {
        $db = \Config\Database::connect();
        
        // Récupérer les informations de la review
        $queryReview = $db->query("
            SELECT 
                r.id_review,
                r.note,
                r.commentaire,
                r.date as date_publication,
                u_auteur.id_utilisateur as auteur_id,
                u_auteur.nom as auteur_nom,
                u_auteur.prenom as auteur_prenom,
                u_auteur.email as auteur_email,
                u_auteur.date_creation as auteur_date_creation,
                u_auteur.type_compte as auteur_type_compte,
                u_auteur.status as auteur_status,
                u_vendeur.id_utilisateur as vendeur_id,
                u_vendeur.nom as vendeur_nom,
                u_vendeur.prenom as vendeur_prenom,
                u_vendeur.email as vendeur_email
            FROM review r
            INNER JOIN utilisateur u_auteur ON r.id_utilisateur_auteur = u_auteur.id_utilisateur
            INNER JOIN utilisateur u_vendeur ON r.id_utilisateur_vendeur = u_vendeur.id_utilisateur
            WHERE r.id_review = ?
        ", [$idReview]);
        
        $review = $queryReview->getRowArray();
        if (!$review) {
            return [];
        }
        
        // Récupérer les signalements pour cette review
        $querySignalements = $db->query("
            SELECT 
                s.id_signalement,
                s.motif,
                s.description,
                s.date,
                s.statut,
                s.raison_decision,
                s.date_traitement,
                u.nom as signaleur_nom,
                u.prenom as signaleur_prenom,
                u.email as signaleur_email,
                u.id_utilisateur as signaleur_id
            FROM signalement s
            INNER JOIN utilisateur u ON s.id_utilisateur_auteur = u.id_utilisateur
            WHERE s.id_review_cible = ?
            ORDER BY s.date DESC
        ", [$idReview]);
        
        $review['signalements'] = $querySignalements->getResultArray();
        
        return $review;
    }

    /**
     * Logger une action administrateur
     *
     * @param string $idAdmin ID de l'administrateur
     * @param string $actionType Type d'action
     * @param string $idCible ID de la cible de l'action
     * @param string|null $raison Raison de l'action
     * @param string|null $ipAddress Adresse IP
     * @return bool True si succès
     */
    public function logAction(
        string $idAdmin,
        string $actionType,
        string $idCible,
        ?string $raison = null,
        ?string $ipAddress = null
    ): bool {
        $db = \Config\Database::connect();
        
        try {
            $data = [
                'action_type' => $actionType,
                'id_cible' => $idCible,
                'raison' => $raison,
                'ip_address' => $ipAddress
            ];
            
            $sql = "SELECT admin_log_create(?, ?::jsonb)";
            $result = $db->query($sql, [$idAdmin, json_encode($data)]);
            
            return $result !== false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur log admin: ' . $e->getMessage());
            return false;
        }
    }
}
