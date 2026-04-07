<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ConsentModel - Gestion des consentements utilisateur (GDPR)
 *
 * Interface avec les procédures stockées pour gérer les consentements:
 * - cookies (bannière cookies)
 * - conditions_utilisation (CGV)
 * - traitement_donnees (politique de confidentialité)
 * - marketing (newsletters, communications)
 *
 * @see database/03-procedures.sql (consentement_*)
 */
class ConsentModel extends Model
{
    protected $table = 'CONSENTEMENT_UTILISATEUR';
    protected $primaryKey = 'id_consentement';
    protected $allowedFields = ['type_consentement', 'statut', 'date_consentement', 'date_retrait', 'id_utilisateur'];

    /**
     * Créer un nouveau consentement pour un utilisateur
     *
     * @param string $idUtilisateur UUID de l'utilisateur
     * @param string $typeConsentement Type: cookies, conditions_utilisation, traitement_donnees, marketing
     * @param bool $statut TRUE = accepté, FALSE = refusé
     * @return array|null Le consentement créé ou null si erreur
     */
    public function creerConsentement(string $idUtilisateur, string $typeConsentement, bool $statut = true): ?array
    {
        $db = \Config\Database::connect();

        try {
            $data = [
                'type_consentement' => $typeConsentement,
                'statut' => $statut
            ];

            $query = $db->query(
                "SELECT * FROM consentement_create(?::uuid, ?::jsonb)",
                [$idUtilisateur, json_encode($data)]
            );

            return $query->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur création consentement: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer tous les consentements d'un utilisateur
     *
     * @param string $idUtilisateur UUID de l'utilisateur
     * @return array Liste des consentements
     */
    public function getConsentements(string $idUtilisateur): array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query(
                "SELECT * FROM consentement_list_by_user(?::uuid)",
                [$idUtilisateur]
            );

            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur récupération consentements: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifier si un utilisateur a donné un consentement spécifique
     *
     * @param string $idUtilisateur UUID de l'utilisateur
     * @param string $typeConsentement Type de consentement à vérifier
     * @return bool TRUE si consentement donné et actif
     */
    public function hasConsent(string $idUtilisateur, string $typeConsentement): bool
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query(
                "SELECT statut FROM CONSENTEMENT_UTILISATEUR
                 WHERE id_utilisateur = ?::uuid
                 AND type_consentement = ?
                 AND statut = TRUE
                 ORDER BY date_consentement DESC
                 LIMIT 1",
                [$idUtilisateur, $typeConsentement]
            );

            $result = $query->getRowArray();
            return $result && $result['statut'] === true;
        } catch (\Exception $e) {
            log_message('error', 'Erreur vérification consentement: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retirer un consentement (met statut à FALSE et date_retrait)
     *
     * @param int $idConsentement ID du consentement
     * @return array|null Le consentement modifié ou null si erreur
     */
    public function retirerConsentement(int $idConsentement): ?array
    {
        $db = \Config\Database::connect();

        try {
            $query = $db->query(
                "SELECT * FROM consentement_retirer(?)",
                [$idConsentement]
            );

            return $query->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur retrait consentement: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Créer les consentements obligatoires lors de l'inscription
     *
     * Crée automatiquement:
     * - conditions_utilisation (CGV)
     * - traitement_donnees (politique confidentialité)
     *
     * @param string $idUtilisateur UUID de l'utilisateur nouvellement créé
     * @return bool TRUE si succès
     */
    public function creerConsentsInscription(string $idUtilisateur): bool
    {
        try {
            // Consentement CGV
            $this->creerConsentement($idUtilisateur, 'conditions_utilisation', true);

            // Consentement traitement données (GDPR)
            $this->creerConsentement($idUtilisateur, 'traitement_donnees', true);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Erreur création consentements inscription: ' . $e->getMessage());
            return false;
        }
    }
}
