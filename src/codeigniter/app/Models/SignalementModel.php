<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\DAOBD;

/**
 * SignalementModel - Modèle pour la gestion des signalements
 * 
 * Gère les signalements de comptes, annonces et avis
 */
class SignalementModel extends Model
{
    protected $table = 'signalement';
    protected $primaryKey = 'id_signalement';
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'motif',
        'description',
        'statut',
        'type',
        'id_review_cible',
        'id_utilisateur_cible',
        'id_annonce_cible',
        'id_utilisateur_auteur'
    ];

    private DAOBD $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new DAOBD('signalement');
    }

    /**
     * Créer un nouveau signalement
     * 
     * Appelle la procédure stockée signalement_create()
     * 
     * @param string $idAuteur ID de l'utilisateur qui signale
     * @param string $type Type de signalement ('user', 'annonce', 'review')
     * @param string $idCible ID de l'entité signalée (utilisateur, annonce, review)
     * @param array $data Données du signalement (motif, description)
     * @return array|null Le signalement créé ou null en cas d'erreur
     */
    public function creerSignalement(string $idAuteur, string $type, string $idCible, array $data): ?array
    {
        // Construire le JSON pour la procédure stockée
        $jsonData = json_encode([
            'motif' => $data['motif'],
            'description' => $data['description'] ?? ''
        ]);

        // Appeler la procédure stockée avec le nouveau paramètre type
        $sql = "SELECT * FROM signalement_create(?, ?, ?::uuid, ?::jsonb)";
        
        $result = $this->db->query($sql, [
            $idAuteur,
            $type,
            $idCible,
            $jsonData
        ]);

        if (!$result) {
            return null;
        }

        return $result->getRowArray();
    }

    /**
     * Récupérer tous les signalements avec filtres
     * 
     * @param array $filters Filtres optionnels (statut, type, etc.)
     * @return array Liste des signalements avec informations enrichies selon le type
     */
    public function listerSignalements(array $filters = []): array
    {
        // Construire la requête SQL avec jointures polymorphes
        $sql = "
            SELECT 
                s.*,
                u_auteur.nom as auteur_nom,
                u_auteur.prenom as auteur_prenom,
                u_auteur.email as auteur_email,
                -- Jointures polymorphes selon le type
                CASE 
                    WHEN s.type = 'user' THEN u_cible.nom
                    WHEN s.type = 'annonce' THEN a.titre
                    WHEN s.type = 'review' THEN 'Avis #' || r.id_review
                END as cible_nom,
                CASE 
                    WHEN s.type = 'user' THEN u_cible.prenom
                    ELSE NULL
                END as cible_prenom,
                CASE 
                    WHEN s.type = 'user' THEN u_cible.email
                    WHEN s.type = 'annonce' THEN 'Annonce' 
                    WHEN s.type = 'review' THEN 'Avis'
                END as cible_email,
                COALESCE(s.id_review_cible, s.id_utilisateur_cible, s.id_annonce_cible) as cible_id
            FROM signalement s
            LEFT JOIN utilisateur u_auteur ON s.id_utilisateur_auteur = u_auteur.id_utilisateur
            LEFT JOIN utilisateur u_cible ON s.id_utilisateur_cible = u_cible.id_utilisateur
            LEFT JOIN annonce a ON s.id_annonce_cible = a.id_annonce
            LEFT JOIN review r ON s.id_review_cible = r.id_review
            WHERE 1=1
        ";

        $params = [];
        
        // Ajouter le filtre de statut si fourni
        if (isset($filters['statut'])) {
            $sql .= " AND s.statut = ?";
            $params[] = $filters['statut'];
        }
        
        // Ajouter le filtre de type si fourni
        if (isset($filters['type'])) {
            $sql .= " AND s.type = ?";
            $params[] = $filters['type'];
        }

        $sql .= " ORDER BY s.date DESC";
        
        $result = $this->db->query($sql, $params);
        
        return $result->getResultArray();
    }

    /**
     * Traiter un signalement (pour les admins)
     * 
     * @param int $idSignalement ID du signalement
     * @param string $decision 'traite' ou 'rejete'
     * @param string|null $raison Raison de la décision
     * @return array|null Le signalement mis à jour ou null
     */
    public function traiterSignalement(int $idSignalement, string $decision, ?string $raison = null): ?array
    {
        $sql = "SELECT * FROM signalement_traiter(?, ?, ?)";
        
        $result = $this->db->query($sql, [
            $idSignalement,
            $decision,
            $raison
        ]);

        if (!$result) {
            return null;
        }

        return $result->getRowArray();
    }

    /**
     * Récupérer un signalement par ID
     * 
     * @param int $id ID du signalement
     * @return array|null Le signalement ou null
     */
    public function getSignalement(int $id): ?array
    {
        return $this->dao->read((string) $id);
    }

    /**
     * Récupérer les signalements d'un utilisateur spécifique
     * 
     * @param string $idUtilisateur ID de l'utilisateur
     * @return array Liste des signalements
     */
    public function getSignalementsByUtilisateur(string $idUtilisateur): array
    {
        return $this->where('id_utilisateur_cible', $idUtilisateur)
                    ->orderBy('date', 'DESC')
                    ->findAll();
    }

    /**
     * Compter les signalements en attente
     * 
     * @return int Nombre de signalements en attente
     */
    public function countEnAttente(): int
    {
        $filters = json_encode(['statut' => 'en_attente']);
        $result = $this->db->query(
            "SELECT COUNT(*) as total FROM signalement_list(?::jsonb)",
            [$filters]
        );

        $row = $result->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Traiter tous les signalements liés à une annonce
     * 
     * @param string $idAnnonce ID de l'annonce
     * @param string $decision 'traite' ou 'rejete'
     * @param string|null $raison Raison de la décision
     * @return bool True si succès
     */
    public function traiterSignalementsAnnonce(string $idAnnonce, string $decision, ?string $raison = null): bool
    {
        try {
            $sql = "
                UPDATE signalement
                SET statut = ?, 
                    raison_decision = ?,
                    date_traitement = NOW()
                WHERE id_annonce_cible = ?::uuid
                AND statut = 'en_attente'
            ";
            
            $this->db->query($sql, [$decision, $raison, $idAnnonce]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Erreur traitement signalements annonce: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Traiter tous les signalements liés à un utilisateur
     * Appelé automatiquement quand un compte est suspendu, banni ou supprimé
     * 
     * @param string $idUtilisateur ID de l'utilisateur
     * @param string $raison Raison de la décision (suspension, bannissement, suppression)
     * @return bool True si succès
     */
    public function traiterSignalementsUtilisateur(string $idUtilisateur, string $raison): bool
    {
        try {
            $sql = "
                UPDATE signalement
                SET statut = 'traite', 
                    raison_decision = ?,
                    date_traitement = NOW()
                WHERE id_utilisateur_cible = ?::uuid
                AND statut = 'en_attente'
            ";
            
            $this->db->query($sql, [$raison, $idUtilisateur]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Erreur traitement signalements utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Traiter tous les signalements liés à une review
     * Appelé quand une review est supprimée ou que les signalements sont rejetés
     * 
     * @param string $idReview ID de la review
     * @param string $decision 'traite' ou 'rejete'
     * @param string|null $raison Raison de la décision
     * @return bool True si succès
     */
    public function traiterSignalementsReview(string $idReview, string $decision, ?string $raison = null): bool
    {
        try {
            $sql = "
                UPDATE signalement
                SET statut = ?, 
                    raison_decision = ?,
                    date_traitement = NOW()
                WHERE id_review_cible = ?::uuid
                AND statut = 'en_attente'
            ";
            
            $this->db->query($sql, [$decision, $raison, $idReview]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Erreur traitement signalements review: ' . $e->getMessage());
            return false;
        }
    }
}
