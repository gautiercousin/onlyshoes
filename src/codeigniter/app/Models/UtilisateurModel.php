<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UtilisateurModel - Modèle pour la gestion des utilisateurs
 *
 * Cette classe gère toutes les opérations liées aux utilisateurs en appelant
 * les procédures stockées PostgreSQL correspondantes.
 * 
 */
class UtilisateurModel extends Model
{
    // Nom de la table (utilisé par CodeIgniter pour les opérations automatiques)
    protected $table = 'utilisateur';

    // Nom de la clé primaire
    protected $primaryKey = 'id_utilisateur';

    // Type de la clé primaire (uuid dans notre cas, mais CodeIgniter attend souvent 'int')
    protected $returnType = 'array';

    // Champs autorisés pour les opérations d'insertion/mise à jour
    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'mdp',
        'type_compte'
    ];

    /**
     * Créer un nouveau compte utilisateur
     *
     * @param array $data Données utilisateur selon le DIAGRAMME DE CLASSES
     *                    ['email' => ..., 'mot_de_passe' => ..., 'nom' => ..., 'prenom' => ...]
     * @return array|null L'utilisateur créé avec son ID, ou null en cas d'erreur
     */
    public function creerCompte(array $data): ?array
    {
        // Vérifier que l'email n'existe pas déjà
        if ($this->where('email', $data['email'])->first()) {
            return null; // Email déjà utilisé
        }

        // Hacher le mot de passe avec bcrypt
        $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);

        // Mapper les champs du diagramme vers la base de données
        $dbData = [
            'email' => $data['email'],
            'mdp' => $hashedPassword,
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'type_compte' => $data['type_compte'] ?? 'standard'
        ];

        $dbData['status'] = $data['status'] ?? 'actif';

        try {
            $sql = "SELECT * FROM utilisateur_create(?::jsonb)";
            $result = $this->db->query($sql, [json_encode($dbData)]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur création utilisateur: ' . $e->getMessage());
            return null;
        }

        $user = $result->getRowArray();
        if (!$user) {
            return null;
        }

        unset($user['mdp']);
        return $user;
    }

    /**
     * Connecter un utilisateur (vérification des credentials)
     *
     * @param string $email Email de l'utilisateur
     * @param string $motDePasse Mot de passe en clair (sera comparé au hash bcrypt)
     * @return array|null Les données utilisateur si credentials valides, null sinon
     *                    En cas d'erreur, retourne ['error' => 'type_erreur', 'status' => 'statut_compte']
     */
    public function seConnecter(string $email, string $motDePasse): ?array
    {
        try {
            $sql = "SELECT (utilisateur_login(?, ?)).*";
            $result = $this->db->query($sql, [$email, $motDePasse]);
            $user = $result->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur login utilisateur: ' . $e->getMessage());
            $user = null;
        }

        // Si l'utilisateur n'existe pas, retourner null
        if (!$user) {
            $user = $this->getUtilisateurParEmail($email);
            if (!$user) {
                return null;
            }
        }

        // Vérifier le statut du compte AVANT la vérification du mot de passe
        $status = $user['status'] ?? 'actif';
        if ($status === 'bannis') {
            return ['error' => 'account_banned', 'status' => 'bannis'];
        }
        if ($status === 'suspendu') {
            return ['error' => 'account_suspended', 'status' => 'suspendu'];
        }

        // Vérifier le mot de passe avec password_verify()
        // $user['mdp'] contient le hash bcrypt, $motDePasse est en clair
        if (!password_verify($motDePasse, $user['mdp'])) {
            return null;
        }

        // Credentials valides! Retirer le mot de passe avant de retourner
        unset($user['mdp']);

        return $user;
    }

    /**
     * Déconnecter un utilisateur
     *
     * Note: La déconnexion est généralement gérée côté session (Controller),
     * mais cette méthode peut servir à logger l'événement ou nettoyer des données
     *
     * @param string $idUtilisateur ID de l'utilisateur
     * @return bool True si succès
     */
    public function seDeconnecter(string $idUtilisateur): bool
    {
        // TODO
        return true;
    }

    /**
     * Modifier les informations personnelles d'un utilisateur
     *
     * Appelle la procédure stockée: utilisateur_update()
     *
     * @param string $idUtilisateur ID de l'utilisateur
     * @param array $data Nouvelles données (nom, prenom, email, etc.)
     * @return array|null L'utilisateur mis à jour, ou null en cas d'erreur
     */
    public function modifierInformations(string $idUtilisateur, array $data): ?array
    {
        $db = \Config\Database::connect();

        try {
            $sql = "SELECT * FROM utilisateur_update(?, ?::jsonb)";
            $result = $db->query($sql, [$idUtilisateur, json_encode($data)]);
            $user = $result->getRowArray();

            if (!$user) {
                return null;
            }

            unset($user['mdp']);
            return $user;
        } catch (\Exception $e) {
            log_message('error', 'Erreur modification utilisateur: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer le compte d'un utilisateur (suppression GDPR-compliant)
     *
     * Appelle la procédure stockée: utilisateur_delete()
     *
     * @param string $idUtilisateur ID de l'utilisateur à supprimer
     * @return bool True si suppression réussie
     */
    public function supprimerCompte(string $idUtilisateur): bool
    {
        $db = \Config\Database::connect();
        
        try {
            $sql = "SELECT utilisateur_delete(?)";
            $result = $db->query($sql, [$idUtilisateur]);
            
            return $result !== false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lister les utilisateurs avec filtres (pour admin)
     *
     * @param array $filters Filtres optionnels (type_compte, status, search)
     * @param int $page Page actuelle (défaut: 1)
     * @param int $perPage Nombre d'éléments par page (défaut: 20)
     * @return array Tableau avec 'utilisateurs' et 'pagination'
     */
    public function listerUtilisateurs(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $db = \Config\Database::connect();
        
        try {
            $filtered = array_filter($filters, static fn($value) => $value !== null && $value !== '');

            if (empty($filtered)) {
                $result = $db->query("SELECT * FROM utilisateur_list()");
            } else {
                $result = $db->query("SELECT * FROM utilisateur_list(?::jsonb)", [json_encode($filtered)]);
            }

            $allUsers = $result->getResultArray();
            $total = count($allUsers);
            
            // Calculer la pagination
            $totalPages = (int) ceil($total / $perPage);
            $page = max(1, min($page, max(1, $totalPages)));
            $offset = ($page - 1) * $perPage;
            
            // Paginer les résultats
            $users = array_slice($allUsers, $offset, $perPage);
            
            foreach ($users as &$user) {
                unset($user['mdp']);
            }
            unset($user);

            return [
                'utilisateurs' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'per_page' => $perPage,
                    'total_items' => $total
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Erreur liste utilisateurs: ' . $e->getMessage());
            return [
                'utilisateurs' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 0,
                    'per_page' => $perPage,
                    'total_items' => 0
                ]
            ];
        }
    }

    /**
     * Changer le status d'un utilisateur (actif, suspendu, bannis)
     *
     * @param string $idUtilisateur ID de l'utilisateur
     * @param string $nouveauStatus Nouveau status (actif, suspendu, bannis)
     * @return bool True si succès
     */
    public function changerStatus(string $idUtilisateur, string $nouveauStatus): bool
    {
        $db = \Config\Database::connect();
        
        try {
            $sql = "SELECT * FROM utilisateur_update(?, ?::jsonb)";
            $result = $db->query($sql, [$idUtilisateur, json_encode(['status' => $nouveauStatus])]);

            return $result !== false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur changement status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Consulter l'historique des achats d'un utilisateur
     *
     * Appelle la procédure stockée: utilisateur_get_historique_achats()
     *
     * @param string $idUtilisateur ID de l'utilisateur
     * @return array Liste des commandes de l'utilisateur
     */
    public function consulterHistoriqueAchats(string $idUtilisateur): array
    {
        $db = \Config\Database::connect();

        try {
            $sql = "SELECT * FROM utilisateur_get_historique_achats(?)";
            $result = $db->query($sql, [$idUtilisateur]);
            return $result->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Erreur historique achats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ajouter une adresse à un utilisateur
     *
     * Appelle la procédure stockée: adresse_create()
     *
     * @param string $idUtilisateur ID de l'utilisateur
     * @param array $adresse Données de l'adresse (rue1, rue2, code_postal, ville, pays)
     * @return array|null L'adresse créée, ou null en cas d'erreur
     */
    public function ajouterAdresse(string $idUtilisateur, array $adresse): ?array
    {
        $db = \Config\Database::connect();

        try {
            $sql = "SELECT * FROM adresse_create(?, ?::jsonb)";
            $result = $db->query($sql, [$idUtilisateur, json_encode($adresse)]);
            return $result->getRowArray() ?: null;
        } catch (\Exception $e) {
            log_message('error', 'Erreur création adresse: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Modifier une adresse existante
     *
     * Appelle la procédure stockée: adresse_update()
     *
     * @param string $idAdresse ID de l'adresse
     * @param array $data Nouvelles données de l'adresse
     * @return array|null L'adresse mise à jour, ou null en cas d'erreur
     */
    public function modifierAdresse(string $idAdresse, array $data): ?array
    {
        $db = \Config\Database::connect();

        try {
            $sql = "SELECT * FROM adresse_update(?, ?::jsonb)";
            $result = $db->query($sql, [(int) $idAdresse, json_encode($data)]);
            return $result->getRowArray() ?: null;
        } catch (\Exception $e) {
            log_message('error', 'Erreur modification adresse: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer une adresse
     *
     * Appelle la procédure stockée: adresse_delete()
     *
     * @param string $idAdresse ID de l'adresse
     * @return bool True si suppression réussie
     */
    public function supprimerAdresse(string $idAdresse): bool
    {
        $db = \Config\Database::connect();

        try {
            $sql = "SELECT adresse_delete(?) AS success";
            $result = $db->query($sql, [(int) $idAdresse]);
            $row = $result->getRowArray();
            return isset($row['success']) ? (bool) $row['success'] : false;
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression adresse: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un utilisateur par son ID
     *
     * Méthode helper pour récupérer les données d'un utilisateur
     *
     * @param string $idUtilisateur ID de l'utilisateur
     * @return array|null Les données utilisateur (selon le diagramme de classes, sans mot_de_passe), ou null
     */
    public function getUtilisateur(string $idUtilisateur): ?array
    {
        $db = \Config\Database::connect();
        $result = $db->query("SELECT * FROM utilisateur_read(?)", [$idUtilisateur]);
        $user = $result->getRowArray();

        // Si l'utilisateur n'existe pas, retourner null
        if (!$user) {
            return null;
        }

        // SÉCURITÉ: Ne JAMAIS retourner le mot de passe
        // unset() supprime la clé 'mdp' du tableau
        unset($user['mdp']);

        // Retourner les données utilisateur sans le mot de passe
        return $user;
    }

    /**
     * Récupérer un utilisateur par email
     *
     * @param string $email Email de l'utilisateur
     * @return array|null Les données utilisateur, ou null si non trouvé
     */
    public function getUtilisateurParEmail(string $email): ?array
    {
        $db = \Config\Database::connect();

        try {
            $filters = ['search' => $email];
            $result = $db->query("SELECT * FROM utilisateur_list(?::jsonb)", [json_encode($filters)]);
            $users = $result->getResultArray();
            foreach ($users as $user) {
                if (($user['email'] ?? '') === $email) {
                    return $user;
                }
            }
            return null;
        } catch (\Exception $e) {
            log_message('error', 'Erreur récupération utilisateur: ' . $e->getMessage());
            return null;
        }
    }
}
