<?php

namespace App\Controllers;

use App\Models\AnnoncesModel;
use CodeIgniter\HTTP\RedirectResponse;

class Paiement extends BaseController
{
    /**
     * Afficher la page de paiement pour une annonce
     *
     * Affiche le formulaire de paiement avec :
     * - Les détails de l'annonce à acheter
     * - L'adresse de livraison pré-remplie (si existante)
     * - Les options de paiement (CB, PayPal, Google Pay, Apple Pay, crypto)
     *
     * Sécurité :
     * - Requiert authentification
     * - Bloque l'auto-achat (vendeur ne peut pas acheter son propre produit)
     *
     * URL: /paiement/{id}
     *
     * @param string $id ID (UUID) de l'annonce à acheter
     * @return string|RedirectResponse Vue de paiement ou redirection si erreur
     */
    public function show(string $id): string|RedirectResponse
    {
        // Vérifier l'authentification
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Vous devez être connecté pour accéder au paiement.');
            return redirect()->to(base_url('connexion'));
        }

        $annoncesModel = new AnnoncesModel();
        $annonce = $annoncesModel->getAnnonce($id);

        // Vérifier que l'annonce existe
        if (!$annonce) {
            session()->setFlashdata('error', 'Annonce introuvable.');
            return redirect()->to(base_url('/'));
        }

        // Bloquer l'achat de son propre produit
        $userId = session()->get('user_id');
        if ($annonce['id_utilisateur_vendeur'] === $userId) {
            session()->setFlashdata('error', 'Vous ne pouvez pas acheter votre propre produit.');
            return redirect()->to(base_url('produit/' . $id));
        }

        // Récupérer l'adresse de l'utilisateur
        $db = \Config\Database::connect();
        $addresses = $db->query("SELECT * FROM adresse_list_by_user(?)", [$userId])->getResultArray();
        $address = !empty($addresses) ? $addresses[0] : null;

        $data = [
            'title' => 'Paiement',
            'id_annonce' => $id,
            'annonce' => $annonce,
            'address' => $address
        ];

        return view('Paiement/page', $data);
    }

    /**
     * Traiter le paiement et créer la commande
     *
     * Workflow complet en transaction :
     * 1. Créer PAIEMENT (via paiement_create)
     * 2. Créer COMMANDE (statut: en_preparation)
     * 3. Créer LIGNE_COMMANDE
     * 4. Lier via DETAILLER_COMMANDE
     * 5. Marquer annonce comme indisponible (via annonce_marquer_indisponible)
     *
     * Sécurité :
     * - Requiert authentification
     * - Bloque l'auto-achat
     * - Valide le type de paiement (carte_bancaire, paypal, google_pay, apple_pay, bitcoin, monero, ethereum)
     *
     * URL: POST /paiement/process/{id}
     * Paramètres POST: type_paiement
     *
     * @param string $id ID (UUID) de l'annonce à acheter
     * @return RedirectResponse Redirection vers /commandes si succès, sinon retour au paiement
     */
    public function process(string $id): RedirectResponse
    {
        log_message('info', 'Payment process started for annonce: ' . $id);

        // Vérifier l'authentification
        if (!session()->get('is_logged_in')) {
            log_message('warning', 'Payment attempt without login');
            session()->setFlashdata('error', 'Vous devez être connecté pour effectuer un paiement.');
            return redirect()->to(base_url('connexion'));
        }

        log_message('info', 'User authenticated: ' . session()->get('user_id'));

        $annoncesModel = new AnnoncesModel();
        $annonce = $annoncesModel->getAnnonce($id);
        $userId = session()->get('user_id');

        // Vérifier que l'annonce existe et est disponible
        if (!$annonce || !$annonce['disponible']) {
            session()->setFlashdata('error', 'Cette annonce n\'est plus disponible.');
            return redirect()->to(base_url('/'));
        }

        // Bloquer l'achat de son propre produit
        if ($annonce['id_utilisateur_vendeur'] === $userId) {
            session()->setFlashdata('error', 'Vous ne pouvez pas acheter votre propre produit.');
            return redirect()->to(base_url('produit/' . $id));
        }

        // Récupérer le type de paiement
        $typePaiement = $this->request->getPost('type_paiement');
        log_message('info', 'Payment type received: ' . ($typePaiement ?? 'NULL'));

        if (empty($typePaiement)) {
            log_message('warning', 'No payment type selected');
            session()->setFlashdata('error', 'Veuillez sélectionner un mode de paiement.');
            return redirect()->to(base_url('paiement/' . $id));
        }

        // Valider le type de paiement
        $typesValides = ['carte_bancaire', 'paypal', 'google_pay', 'apple_pay', 'bitcoin', 'monero', 'ethereum'];
        if (!in_array($typePaiement, $typesValides)) {
            session()->setFlashdata('error', 'Mode de paiement invalide.');
            return redirect()->to(base_url('paiement/' . $id));
        }

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // 1. Créer le paiement
            $paiementQuery = $db->query(
                "SELECT * FROM paiement_create(?::jsonb)",
                [json_encode([
                    'type' => $typePaiement,
                    'statut' => 'valide',
                    'montant_paye' => $annonce['prix']
                ])]
            );
            $paiement = $paiementQuery->getRowArray();

            // 2. Créer la commande
            $commandeQuery = $db->query(
                "INSERT INTO COMMANDE (id_utilisateur, id_paiement, statut)
                 VALUES (?, ?, 'en_preparation')
                 RETURNING *",
                [$userId, $paiement['id_paiement']]
            );
            $commande = $commandeQuery->getRowArray();

            // 3. Créer la ligne de commande
            $ligneCommandeQuery = $db->query(
                "INSERT INTO LIGNE_COMMANDE (prix, quantite)
                 VALUES (?, 1)
                 RETURNING id_ligne_commande",
                [$annonce['prix']]
            );
            $ligneCommande = $ligneCommandeQuery->getRowArray();

            // 4. Lier commande, annonce et ligne via DETAILLER_COMMANDE
            $db->query(
                "INSERT INTO DETAILLER_COMMANDE (id_commande, id_annonce, id_ligne_commande)
                 VALUES (?, ?, ?)",
                [$commande['id_commande'], $id, $ligneCommande['id_ligne_commande']]
            );

            // 5. Marquer l'annonce comme indisponible
            $db->query("SELECT annonce_marquer_indisponible(?::uuid)", [$id]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Une erreur est survenue lors du traitement du paiement.');
                return redirect()->to(base_url('paiement/' . $id));
            }

            // Succès
            session()->setFlashdata('success', 'Paiement effectué avec succès ! Votre commande est en préparation.');
            return redirect()->to(base_url('commandes'));

        } catch (\Exception $e) {
            log_message('error', 'Erreur paiement: ' . $e->getMessage());
            session()->setFlashdata('error', 'Une erreur est survenue lors du traitement du paiement.');
            return redirect()->to(base_url('paiement/' . $id));
        }
    }
}
