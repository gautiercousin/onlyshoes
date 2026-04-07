<?php

namespace App\Controllers;

use App\Models\SignalementModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Signalement extends BaseController
{
    private function sanitizeReturnUrl(?string $returnUrl): string
    {
        if (!$returnUrl) {
            return '/';
        }

        if (str_starts_with($returnUrl, '/')) {
            return $returnUrl;
        }

        return '/';
    }
    /**
     * Afficher le formulaire de signalement
     * 
     * @param string $type Type de signalement ('user', 'annonce', 'review')
     * @param string $id ID de l'entité à signaler
     */
    public function show(string $type, string $id): string
    {
        // Vérifier que l'utilisateur est authentifié
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté pour signaler.');
            return redirect()->to(base_url('connexion'));
        }

        // Valider le type
        if (!in_array($type, ['user', 'annonce', 'review'])) {
            throw PageNotFoundException::forPageNotFound("Type de signalement invalide");
        }

        // Vérifier qu'on ne se signale pas soi-même (pour les users)
        if ($type === 'user' && $id === session()->get('user_id')) {
            session()->setFlashdata('error', 'Vous ne pouvez pas vous signaler vous-même.');
            return redirect()->to(base_url('/'));
        }

        $db = \Config\Database::connect();
        
        // Récupérer l'URL de retour (d'où vient l'utilisateur)
        $returnUrl = $this->sanitizeReturnUrl($this->request->getGet('return_url'));

        // Récupérer les infos selon le type
        $cibleData = $this->getCibleData($db, $type, $id);
        
        if (!$cibleData) {
            throw PageNotFoundException::forPageNotFound(ucfirst($type) . " $id introuvable");
        }
        
        $data = [
            'type' => $type,
            'cible' => $cibleData,
            'id_cible' => $id,
            'return_url' => $returnUrl
        ];
        return view('Signalement/Page', $data);
    }
    
    /**
     * Récupérer les données de la cible selon le type
     */
    private function getCibleData($db, string $type, string $id): ?array
    {
        switch ($type) {
            case 'user':
                $query = $db->query("SELECT * FROM utilisateur_read(?)", [$id]);
                $row = $query->getRowArray();
                return $row ? [
                    'nom' => $row['nom'],
                    'prenom' => $row['prenom'],
                    'email' => $row['email']
                ] : null;
                
            case 'annonce':
                $query = $db->query("SELECT * FROM annonce_read(?::uuid)", [$id]);
                $row = $query->getRowArray();
                return $row ? [
                    'titre' => $row['titre'],
                    'description' => substr($row['description'], 0, 100) . '...',
                    'prix' => $row['prix']
                ] : null;
                
            case 'review':
                $query = $db->query("SELECT * FROM review_read(?::uuid)", [$id]);
                $row = $query->getRowArray();
                return $row ? [
                    'note' => $row['note'],
                    'commentaire' => substr($row['commentaire'] ?? '', 0, 100),
                    'date' => $row['date']
                ] : null;
                
            default:
                return null;
        }
    }

    /**
     * Traiter le formulaire de signalement (POST)
     */
    public function create()
    {
        // Vérifier que l'utilisateur est authentifié
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté pour signaler.');
            return redirect()->to(base_url('connexion'));
        }

        // Récupérer les données du formulaire
        $type = $this->request->getPost('type');
        $idCible = $this->request->getPost('id_cible');
        $motif = $this->request->getPost('motif');
        $description = $this->request->getPost('description');
        $returnUrl = $this->sanitizeReturnUrl($this->request->getPost('return_url'));

        // Validation du type
        if (!in_array($type, ['user', 'annonce', 'review'])) {
            session()->setFlashdata('error', 'Type de signalement invalide.');
            return redirect()->to(base_url($returnUrl));
        }

        // Validation
        if (empty($motif)) {
            session()->setFlashdata('error', 'Veuillez sélectionner un motif.');
            $formUrl = base_url('signalement/' . $type . '/' . $idCible) . '?return_url=' . urlencode($returnUrl);
            return redirect()->to($formUrl)->withInput();
        }

        // La description est obligatoire
        if (empty($description)) {
            session()->setFlashdata('error', 'La description est obligatoire.');
            $formUrl = base_url('signalement/' . $type . '/' . $idCible) . '?return_url=' . urlencode($returnUrl);
            return redirect()->to($formUrl)->withInput();
        }

        // Vérifier qu'on ne se signale pas soi-même (pour les users)
        if ($type === 'user' && $idCible === session()->get('user_id')) {
            session()->setFlashdata('error', 'Vous ne pouvez pas vous signaler vous-même.');
            return redirect()->to(base_url($returnUrl));
        }

        // Créer le signalement via le Model
        $signalementModel = new SignalementModel();
        
        $signalementData = [
            'motif' => $motif,
            'description' => $description ?? ''
        ];

        $result = $signalementModel->creerSignalement(
            session()->get('user_id'),
            $type,
            $idCible,
            $signalementData
        );

        if ($result) {
            // TODO: Notifier l'administrateur (email, notification, etc.)
            
            // Rediriger vers la page de confirmation
            return redirect()->to(base_url('signalement/confirmation'));
        } else {
            session()->setFlashdata('error', 'Une erreur est survenue lors de l\'enregistrement du signalement.');
            $formUrl = base_url('signalement/' . $type . '/' . $idCible) . '?return_url=' . urlencode($returnUrl);
            return redirect()->to($formUrl)->withInput();
        }
    }

    /**
     * Afficher la page de confirmation après un signalement réussi
     */
    public function confirmation(): string
    {
        return view('Signalement/Confirmation');
    }
}
