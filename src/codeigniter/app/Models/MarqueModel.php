<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\DAOBD;

/**
 * MarqueModel - Modèle pour la consultation des marques
 * Utilise le pattern DAO pour les opérations CRUD simples
 */
class MarqueModel extends Model
{
    protected $table = 'marque';
    protected $primaryKey = 'id_marque';
    protected $returnType = 'array';

    private DAOBD $dao;

    public function __construct()
    {
        parent::__construct();
        // Utilisation du DAO générique pour cette table simple
        $this->dao = new DAOBD('marque');
    }

    /**
     * Lister toutes les marques via le DAO
     *
     * @return array
     */
    public function listerMarques(): array
    {
        return $this->dao->readAll();
    }

    /**
     * Obtenir une marque par son ID
     *
     * @param string $id
     * @return array|null
     */
    public function getMarque(string $id): ?array
    {
        return $this->dao->read($id);
    }

    /**
     * Créer une nouvelle marque
     *
     * @param string $nom
     * @return array|null
     */
    public function creerMarque(string $nom): ?array
    {
        try {
            return $this->dao->create(['nom' => $nom]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur création marque: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer une marque
     *
     * @param string $id
     * @return bool
     */
    public function supprimerMarque(string $id): bool
    {
        try {
            return $this->dao->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression marque: ' . $e->getMessage());
            return false;
        }
    }
}
