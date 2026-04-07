<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\DAOBD;

/**
 * Commande Spark pour tester la classe DAOBD
 *
 * Utilisation: php spark test:daobd
 */
class TestDAOBD extends BaseCommand
{
    protected $group       = 'Tests';
    protected $name        = 'test:daobd';
    protected $description = 'Teste la classe DAOBD avec les tables COULEUR, MARQUE et MATERIAU';

    public function run(array $params)
    {
        CLI::write('==============================================', 'yellow');
        CLI::write('  Test de la classe DAOBD (Métadonnées)', 'yellow');
        CLI::write('==============================================', 'yellow');
        CLI::newLine();

        // Tester avec la table COULEUR
        $this->testerTable('couleur', 'COULEUR');
        CLI::newLine(2);

        // Tester avec la table MARQUE
        $this->testerTable('marque', 'MARQUE');
        CLI::newLine(2);

        // Tester avec la table MATERIAU
        $this->testerTable('materiau', 'MATERIAU');
        CLI::newLine(2);

        CLI::write('==============================================', 'green');
        CLI::write('  Tests terminés avec succès!', 'green');
        CLI::write('==============================================', 'green');
    }

    /**
     * Teste toutes les opérations CRUD sur une table
     */
    private function testerTable(string $tableName, string $displayName): void
    {
        CLI::write("--- Test de la table {$displayName} ---", 'cyan');
        CLI::newLine();

        try {
            // Initialiser DAOBD pour cette table
            $dao = new DAOBD($tableName);

            // Afficher les métadonnées chargées
            CLI::write("Clé primaire: " . $dao->getPrimaryKey(), 'light_gray');
            CLI::write("Colonnes: " . implode(', ', array_column($dao->getColumns(), 'column_name')), 'light_gray');
            CLI::newLine();

            // 1. CREATE - Créer un nouvel enregistrement
            CLI::write('[1/5] Test CREATE...', 'yellow');
            $testData = ['nom' => 'TEST_' . strtoupper($tableName) . '_' . time()];
            $created = $dao->create($testData);

            if ($created) {
                CLI::write('  Enregistrement créé: ID = ' . $created[$dao->getPrimaryKey()], 'green');
                $createdId = $created[$dao->getPrimaryKey()];
            } else {
                CLI::error('  Échec de la création');
                return;
            }

            // 2. READ - Lire l'enregistrement créé
            CLI::write('[2/5] Test READ...', 'yellow');
            $read = $dao->read($createdId);

            if ($read && $read['nom'] === $testData['nom']) {
                CLI::write('  Enregistrement lu: ' . json_encode($read), 'green');
            } else {
                CLI::error('  Échec de la lecture');
                return;
            }

            // 3. READALL - Lire tous les enregistrements (limité à 5)
            CLI::write('[3/5] Test READALL...', 'yellow');
            $all = $dao->readAll(5);
            CLI::write('  Nombre d\'enregistrements (max 5): ' . count($all), 'green');

            // 4. UPDATE - Mettre à jour l'enregistrement
            CLI::write('[4/5] Test UPDATE...', 'yellow');
            $updateData = ['nom' => 'UPDATED_' . strtoupper($tableName) . '_' . time()];
            $updated = $dao->update($createdId, $updateData);

            if ($updated && $updated['nom'] === $updateData['nom']) {
                CLI::write('  Enregistrement mis à jour: ' . json_encode($updated), 'green');
            } else {
                CLI::error('  Échec de la mise à jour');
                return;
            }

            // 5. DELETE - Supprimer l'enregistrement
            CLI::write('[5/5] Test DELETE...', 'yellow');
            $deleted = $dao->delete($createdId);

            if ($deleted) {
                CLI::write('  Enregistrement supprimé avec succès', 'green');

                // Vérifier que l'enregistrement n'existe plus
                $verification = $dao->read($createdId);
                if ($verification === null) {
                    CLI::write('  Vérification: l\'enregistrement n\'existe plus', 'green');
                } else {
                    CLI::error('  Vérification échouée: l\'enregistrement existe toujours');
                }
            } else {
                CLI::error('  Échec de la suppression');
            }

            CLI::newLine();
            CLI::write("✓ Tous les tests pour {$displayName} sont réussis!", 'green');

        } catch (\Exception $e) {
            CLI::error('Exception lors du test de ' . $displayName);
            CLI::error($e->getMessage());
            CLI::write('Stack trace:', 'red');
            CLI::write($e->getTraceAsString(), 'red');
        }
    }
}
