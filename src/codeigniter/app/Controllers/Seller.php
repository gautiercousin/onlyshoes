<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Seller extends BaseController
{
    /**
     * Afficher la page d'un vendeur
     *
     * Redirige vers la page de profil utilisateur (ancien format /vendeur/{id} -> /utilisateur/profil/{id})
     *
     * URL: /vendeur/{id}
     *
     * @param string $id ID (UUID) du vendeur
     * @return string Redirection vers le profil utilisateur
     */
    public function show($id): string
    {
        return redirect()->to(base_url('utilisateur/profil/' . $id));
    }
}
