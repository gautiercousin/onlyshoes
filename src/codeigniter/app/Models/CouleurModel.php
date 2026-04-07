<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\DAOBD;

/**
 * CouleurModel - Modèle pour la consultation des couleurs
 * Utilise le pattern DAO pour les opérations CRUD simples
 */
class CouleurModel extends Model
{
    protected $table = 'couleur';
    protected $primaryKey = 'id_couleur';
    protected $returnType = 'array';

    private DAOBD $dao;

    public function __construct()
    {
        parent::__construct();
        // Utilisation du DAO générique pour cette table simple
        $this->dao = new DAOBD('couleur');
    }

    /**
     * Lister toutes les couleurs via le DAO
     *
     * @return array
     */
    public function listerCouleurs(): array
    {
        return $this->dao->readAll();
    }

    /**
     * Obtenir une couleur par son ID
     *
     * @param string $id
     * @return array|null
     */
    public function getCouleur(string $id): ?array
    {
        return $this->dao->read($id);
    }

    /**
     * Créer une nouvelle couleur
     *
     * @param string $nom
     * @return array|null
     */
    public function creerCouleur(string $nom): ?array
    {
        try {
            return $this->dao->create(['nom' => $nom]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur création couleur: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer une couleur
     *
     * @param string $id
     * @return bool
     */
    public function supprimerCouleur(string $id): bool
    {
        try {
            return $this->dao->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression couleur: ' . $e->getMessage());
            return false;
        }
    }
}
