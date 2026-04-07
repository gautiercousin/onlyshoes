<?php

namespace App\Libraries;

// Importer la classe Database depuis le namespace Config
// Permet d'accéder à la connexion base de données de CodeIgniter
use Config\Database;

/**
 * DAOBD - Data Access Object pour la Base de Données
 *
 * Classe générique utilisant les métadonnées PostgreSQL (information_schema)
 * pour effectuer des opérations CRUD sur n'importe quelle table.
 *
 * Utilise PHP pour interroger directement les métadonnées et construire
 * les requêtes SQL dynamiquement.
 *
 * Utilisation:
 *   $dao = new DAOBD('couleur');
 *   $dao->create(['nom' => 'Rouge']);
 *   $couleur = $dao->read('1');
 */
class DAOBD
{
    // Propriété qui stocke l'objet de connexion à la base de données CodeIgniter
    protected $db;

    // Nom de la table PostgreSQL sur laquelle on travaille (ex: 'couleur', 'marque')
    protected $table;

    // Nom de la colonne clé primaire (chargé depuis information_schema)
    // Ex: 'id_couleur', 'id_marque'
    protected $primaryKey;

    // Tableau contenant les métadonnées de toutes les colonnes de la table
    // Format: [['column_name' => 'id', 'data_type' => 'integer', ...], ...]
    protected $columns;

    /**
     * Constructeur - Appelé automatiquement quand on fait: new DAOBD('couleur')
     *
     * @param string $table Nom de la table à manipuler (ex: 'couleur', 'marque')
     */
    public function __construct(string $table)
    {
        // Stocker le nom de la table dans la propriété $this->table
        $this->table = $table;

        // Database::connect() est une méthode statique de CodeIgniter
        // Elle retourne un objet de connexion à la base de données PostgreSQL
        // Cet objet permet d'exécuter des requêtes SQL
        $this->db = Database::connect();

        // Appeler notre méthode loadMetadata() pour charger les infos de la table
        // depuis information_schema (clé primaire + colonnes)
        $this->loadMetadata();
    }

    /**
     * Charge les métadonnées de la table depuis information_schema
     *
     * information_schema est une base de données système PostgreSQL qui contient
     * des informations sur la structure de toutes les tables (métadonnées)
     */
    protected function loadMetadata(): void
    {
        // ========== ÉTAPE 1: Récupérer la clé primaire ==========

        // Requête SQL pour trouver le nom de la colonne clé primaire
        // ? = placeholder qui sera remplacé par $this->table de manière sécurisée
        $pkQuery = "
            SELECT kcu.column_name
            FROM information_schema.key_column_usage kcu
            JOIN information_schema.table_constraints tc
                ON kcu.constraint_name = tc.constraint_name
            WHERE tc.table_name = ?
                AND tc.constraint_type = 'PRIMARY KEY'
            LIMIT 1
        ";
        // information_schema.key_column_usage : contient toutes les colonnes des contraintes
        // information_schema.table_constraints : contient toutes les contraintes (PRIMARY KEY, FOREIGN KEY, etc.)
        // ON kcu.constraint_name = tc.constraint_name : lie les deux tables
        // tc.constraint_type = 'PRIMARY KEY' : filtre pour garder uniquement la clé primaire

        // Exécuter la requête SQL
        // $this->db->query() est une méthode CodeIgniter qui exécute du SQL
        // Le tableau [$this->table] remplace le ? dans la requête (protection injection SQL)
        $result = $this->db->query($pkQuery, [$this->table]);

        // getRow() retourne la première ligne du résultat sous forme d'objet
        // Ex: $row->column_name sera 'id_couleur' pour la table couleur
        $row = $result->getRow();

        // Si $row est null/false, c'est que la table n'existe pas ou n'a pas de clé primaire
        if (!$row) {
            // throw = lancer une exception (erreur fatale qui arrête l'exécution)
            // \Exception = classe PHP standard pour les erreurs
            throw new \Exception("Table '{$this->table}' n'a pas de clé primaire ou n'existe pas");
        }

        // Stocker le nom de la colonne clé primaire (ex: 'id_couleur')
        // $row->column_name contient la valeur de la colonne column_name du SELECT
        $this->primaryKey = $row->column_name;

        // ========== ÉTAPE 2: Récupérer toutes les colonnes ==========

        // Requête SQL pour obtenir la liste de toutes les colonnes de la table
        $colQuery = "
            SELECT column_name, data_type, column_default
            FROM information_schema.columns
            WHERE table_name = ?
            ORDER BY ordinal_position
        ";
        // information_schema.columns : contient une ligne par colonne de chaque table
        // column_name : nom de la colonne (ex: 'nom', 'id_couleur')
        // data_type : type de données (ex: 'integer', 'character varying')
        // column_default : valeur par défaut (ex: 'nextval(...)')
        // ordinal_position : position de la colonne dans la table (1, 2, 3...)

        // Exécuter la requête
        $result = $this->db->query($colQuery, [$this->table]);

        // getResultArray() retourne TOUTES les lignes sous forme de tableau associatif
        // Ex: [
        //   ['column_name' => 'id_couleur', 'data_type' => 'integer', ...],
        //   ['column_name' => 'nom', 'data_type' => 'character varying', ...]
        // ]
        $this->columns = $result->getResultArray();
    }

    /**
     * CREATE - Insérer un nouvel enregistrement
     *
     * @param array $data Données à insérer (tableau associatif ex: ['nom' => 'Rouge'])
     * @return array|null L'enregistrement créé avec son ID, ou null en cas d'erreur
     */
    public function create(array $data): ?array
    {
        // try/catch = gestion d'erreurs en PHP
        // Si une erreur se produit dans try{}, le code dans catch{} s'exécute
        try {
            // ========== ÉTAPE 1: Filtrer les colonnes valides ==========

            // array_column() extrait une colonne d'un tableau 2D
            // $this->columns = [['column_name' => 'id_couleur', ...], ['column_name' => 'nom', ...]]
            // array_column($this->columns, 'column_name') = ['id_couleur', 'nom']
            $validColumns = array_column($this->columns, 'column_name');

            // array_flip() inverse clés et valeurs d'un tableau
            // ['id_couleur', 'nom'] devient ['id_couleur' => 0, 'nom' => 1]
            // Pourquoi? Pour utiliser array_intersect_key() qui compare les CLÉS

            // array_intersect_key() garde uniquement les clés qui existent dans les deux tableaux
            // Ex: $data = ['nom' => 'Rouge', 'invalid' => 'test']
            //     $validColumns = ['id_couleur' => 0, 'nom' => 1]
            //     Résultat: ['nom' => 'Rouge'] (invalid est supprimé car pas dans validColumns)
            $filteredData = array_intersect_key($data, array_flip($validColumns));

            // ========== ÉTAPE 2: Construire la requête INSERT dynamiquement ==========

            // array_keys() retourne les clés d'un tableau associatif
            // ['nom' => 'Rouge'] devient ['nom']
            $columns = array_keys($filteredData);

            // array_fill(start_index, count, value) crée un tableau rempli d'une valeur
            // array_fill(0, 2, '?') = ['?', '?']
            // On crée autant de ? que de colonnes pour la requête SQL
            $placeholders = array_fill(0, count($columns), '?');

            // sprintf() formate une chaîne (comme printf en C)
            // %s = remplace par une chaîne de caractères
            // escapeIdentifiers() protège contre l'injection SQL pour les noms de table/colonne
            // implode(', ', $array) joint les éléments d'un tableau avec ', '
            // Ex: ['nom', 'prix'] devient 'nom, prix'

            // array_map(fonction, tableau) applique une fonction à chaque élément
            // [$this->db, 'escapeIdentifiers'] = appeler la méthode escapeIdentifiers sur $this->db
            // Cela protège CHAQUE nom de colonne contre l'injection SQL

            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s) RETURNING *",
                $this->db->escapeIdentifiers($this->table),  // Nom de table sécurisé
                implode(', ', array_map([$this->db, 'escapeIdentifiers'], $columns)),  // Colonnes sécurisées
                implode(', ', $placeholders)  // '?, ?'
            );
            // Exemple de SQL généré:
            // INSERT INTO "couleur" ("nom") VALUES (?) RETURNING *
            // RETURNING * fait que PostgreSQL retourne la ligne insérée (avec l'ID auto-généré)

            // ========== ÉTAPE 3: Exécuter la requête ==========

            // array_values() retourne uniquement les valeurs du tableau (sans les clés)
            // ['nom' => 'Rouge'] devient ['Rouge']
            // Les ? dans la requête seront remplacés par ces valeurs dans l'ordre
            $result = $this->db->query($sql, array_values($filteredData));

            // Si la requête a échoué, $result sera false
            if (!$result) {
                // log_message() est une fonction CodeIgniter pour logger les erreurs
                // 'error' = niveau de gravité
                log_message('error', "DAOBD::create - Erreur lors de l'insertion dans {$this->table}");
                return null;  // Retourner null pour indiquer l'échec
            }

            // getRowArray() retourne la première ligne du résultat sous forme de tableau associatif
            // Grâce à RETURNING *, on récupère la ligne insérée avec son ID
            // Ex: ['id_couleur' => 1, 'nom' => 'Rouge']
            return $result->getRowArray();

        } catch (\Exception $e) {
            // Si une exception (erreur) est levée, on arrive ici
            // $e->getMessage() retourne le message d'erreur
            log_message('error', "DAOBD::create - Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * READ - Lire un enregistrement par son ID
     *
     * @param string $id ID de l'enregistrement (ex: '1', 'uuid-xxx')
     * @return array|null L'enregistrement sous forme de tableau, ou null si non trouvé
     */
    public function read(string $id): ?array
    {
        try {
            // Construire une requête SELECT avec WHERE sur la clé primaire
            // sprintf() formate la chaîne avec les %s remplacés
            // escapeIdentifiers() protège les noms de table/colonne
            $sql = sprintf(
                "SELECT * FROM %s WHERE %s = ?",
                $this->db->escapeIdentifiers($this->table),  // Ex: "couleur"
                $this->db->escapeIdentifiers($this->primaryKey)  // Ex: "id_couleur"
            );
            // SQL généré: SELECT * FROM "couleur" WHERE "id_couleur" = ?

            // Exécuter la requête avec $id comme paramètre
            // [$id] = tableau contenant la valeur qui remplacera le ?
            $result = $this->db->query($sql, [$id]);

            if (!$result) {
                log_message('error', "DAOBD::read - Erreur lors de la lecture dans {$this->table}");
                return null;
            }

            // getRowArray() retourne la première ligne ou null si aucun résultat
            // Ex: ['id_couleur' => 1, 'nom' => 'Rouge'] ou null
            return $result->getRowArray();

        } catch (\Exception $e) {
            log_message('error', "DAOBD::read - Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * READ ALL - Lire tous les enregistrements
     *
     * @param int $limit Limite du nombre de résultats (0 = pas de limite, défaut)
     * @return array Tableau d'enregistrements (tableau de tableaux associatifs)
     */
    public function readAll(int $limit = 0): array
    {
        try {
            // Construire un SELECT * simple
            $sql = sprintf(
                "SELECT * FROM %s",
                $this->db->escapeIdentifiers($this->table)
            );

            // Si une limite est spécifiée (> 0), ajouter LIMIT à la requête
            if ($limit > 0) {
                // .= signifie "ajouter à la fin de la chaîne"
                // (int)$limit force la conversion en entier pour la sécurité
                $sql .= " LIMIT " . (int)$limit;
            }
            // SQL généré: SELECT * FROM "couleur" LIMIT 10

            // Exécuter la requête (pas de paramètres cette fois)
            $result = $this->db->query($sql);

            if (!$result) {
                log_message('error', "DAOBD::readAll - Erreur lors de la lecture dans {$this->table}");
                return [];  // Retourner tableau vide en cas d'erreur
            }

            // getResultArray() retourne TOUTES les lignes sous forme de tableau
            // Ex: [
            //   ['id_couleur' => 1, 'nom' => 'Rouge'],
            //   ['id_couleur' => 2, 'nom' => 'Bleu']
            // ]
            return $result->getResultArray();

        } catch (\Exception $e) {
            log_message('error', "DAOBD::readAll - Exception: " . $e->getMessage());
            return [];  // Retourner tableau vide en cas d'exception
        }
    }

    /**
     * UPDATE - Mettre à jour un enregistrement
     *
     * @param string $id ID de l'enregistrement à modifier
     * @param array $data Données à mettre à jour (ex: ['nom' => 'Nouveau nom'])
     * @return array|null L'enregistrement mis à jour, ou null en cas d'erreur
     */
    public function update(string $id, array $data): ?array
    {
        try {
            // ========== ÉTAPE 1: Filtrer les colonnes valides (comme dans create()) ==========
            $validColumns = array_column($this->columns, 'column_name');
            $filteredData = array_intersect_key($data, array_flip($validColumns));

            // ========== ÉTAPE 2: Empêcher la modification de la clé primaire ==========
            // unset() supprime un élément d'un tableau
            // On ne veut JAMAIS permettre de modifier l'ID
            unset($filteredData[$this->primaryKey]);

            // Si après filtrage il ne reste rien à modifier, retourner null
            // empty() retourne true si le tableau est vide
            if (empty($filteredData)) {
                return null;
            }

            // ========== ÉTAPE 3: Construire la clause SET dynamiquement ==========
            // Pour UPDATE, on doit construire: SET colonne1 = ?, colonne2 = ?

            $setParts = [];  // Tableau pour stocker chaque "colonne = ?"
            $values = [];    // Tableau pour stocker les valeurs

            // foreach parcourt chaque élément du tableau
            // $column = clé (nom de colonne), $value = valeur
            foreach ($filteredData as $column => $value) {
                // Ajouter "colonne = ?" à $setParts (avec protection injection SQL)
                $setParts[] = $this->db->escapeIdentifiers($column) . ' = ?';

                // Ajouter la valeur au tableau $values
                $values[] = $value;
            }
            // Ex: $setParts = ['"nom" = ?', '"prix" = ?']
            //     $values = ['Nouveau nom', 99.99]

            // Ajouter l'ID à la fin de $values pour le WHERE
            $values[] = $id;
            // $values = ['Nouveau nom', 99.99, '1']

            // ========== ÉTAPE 4: Construire la requête UPDATE ==========
            $sql = sprintf(
                "UPDATE %s SET %s WHERE %s = ? RETURNING *",
                $this->db->escapeIdentifiers($this->table),  // Nom de table
                implode(', ', $setParts),  // "nom" = ?, "prix" = ?
                $this->db->escapeIdentifiers($this->primaryKey)  // id_couleur
            );
            // SQL généré: UPDATE "couleur" SET "nom" = ? WHERE "id_couleur" = ? RETURNING *

            // ========== ÉTAPE 5: Exécuter la requête ==========
            // $values contient toutes les valeurs dans l'ordre: [valeurs SET..., id WHERE]
            $result = $this->db->query($sql, $values);

            if (!$result) {
                log_message('error', "DAOBD::update - Erreur lors de la mise à jour dans {$this->table}");
                return null;
            }

            // RETURNING * retourne la ligne mise à jour
            return $result->getRowArray();

        } catch (\Exception $e) {
            log_message('error', "DAOBD::update - Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * DELETE - Supprimer un enregistrement
     *
     * @param string $id ID de l'enregistrement à supprimer
     * @return bool True si suppression réussie, false sinon
     */
    public function delete(string $id): bool
    {
        try {
            // Construire une requête DELETE simple avec WHERE sur la clé primaire
            $sql = sprintf(
                "DELETE FROM %s WHERE %s = ?",
                $this->db->escapeIdentifiers($this->table),  // Nom de table
                $this->db->escapeIdentifiers($this->primaryKey)  // Nom de la clé primaire
            );
            // SQL généré: DELETE FROM "couleur" WHERE "id_couleur" = ?

            // Exécuter la requête avec l'ID comme paramètre
            $result = $this->db->query($sql, [$id]);

            if (!$result) {
                log_message('error', "DAOBD::delete - Erreur lors de la suppression dans {$this->table}");
                return false;  // Retourner false en cas d'erreur
            }

            // affectedRows() retourne le nombre de lignes affectées par la dernière requête
            // Si > 0, cela signifie qu'au moins une ligne a été supprimée
            // Si = 0, cela signifie qu'aucune ligne n'a été trouvée avec cet ID
            return $this->db->affectedRows() > 0;

        } catch (\Exception $e) {
            log_message('error', "DAOBD::delete - Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère le nom de la table
     *
     * Méthode getter simple pour accéder à la propriété protected $table
     *
     * @return string Nom de la table (ex: 'couleur', 'marque')
     */
    public function getTable(): string
    {
        // Retourner la valeur de la propriété $this->table
        return $this->table;
    }

    /**
     * Récupère le nom de la clé primaire
     *
     * Méthode getter pour accéder à la propriété protected $primaryKey
     * Utile pour savoir quelle est la colonne ID de la table
     *
     * @return string Nom de la colonne clé primaire (ex: 'id_couleur', 'id_marque')
     */
    public function getPrimaryKey(): string
    {
        // Retourner la valeur de la propriété $this->primaryKey
        // Cette valeur a été chargée depuis information_schema dans loadMetadata()
        return $this->primaryKey;
    }

    /**
     * Récupère les métadonnées des colonnes
     *
     * Méthode getter pour accéder aux métadonnées complètes de toutes les colonnes
     * Utile pour afficher les informations de la table ou faire du debug
     *
     * @return array Tableau de métadonnées
     *               Format: [
     *                 ['column_name' => 'id_couleur', 'data_type' => 'integer', 'column_default' => '...'],
     *                 ['column_name' => 'nom', 'data_type' => 'character varying', ...]
     *               ]
     */
    public function getColumns(): array
    {
        // Retourner le tableau complet des métadonnées des colonnes
        // Ce tableau a été chargé depuis information_schema dans loadMetadata()
        return $this->columns;
    }
}
