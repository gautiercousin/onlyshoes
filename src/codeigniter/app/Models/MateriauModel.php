<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\DAOBD;

/**
 * MateriauModel - Modèle pour la consultation des matériaux
 * Utilise le pattern DAO pour les opérations CRUD simples
 */
class MateriauModel extends Model
{
    protected $table = 'materiau';
    protected $primaryKey = 'id_materiau';
    protected $returnType = 'array';

    private DAOBD $dao;

    public function __construct()
    {
        parent::__construct();
        // Utilisation du DAO générique pour cette table simple
        $this->dao = new DAOBD('materiau');
    }

    /**
     * Lister tous les matériaux via le DAO
     *
     * @return array
     */
    public function listerMateriaux(): array
    {
        return $this->dao->readAll();
    }

    /**
     * Obtenir un matériau par son ID
     *
     * @param string $id
     * @return array|null
     */
    public function getMateriau(string $id): ?array
    {
        return $this->dao->read($id);
    }

    /**
     * Créer un nouveau matériau
     *
     * @param string $nom
     * @return array|null
     */
    public function creerMateriau(string $nom): ?array
    {
        try {
            return $this->dao->create(['nom' => $nom]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur création matériau: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer un matériau
     *
     * @param string $id
     * @return bool
     */
    public function supprimerMateriau(string $id): bool
    {
        try {
            return $this->dao->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression matériau: ' . $e->getMessage());
            return false;
        }
    }
}
