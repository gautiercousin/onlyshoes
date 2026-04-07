<?php

namespace App\Controllers;

/**
 * Legal Controller
 *
 * Gère les pages légales (RGPD, CGV, mentions légales)
 */
class Legal extends BaseController
{
    /**
     * Politique de confidentialité (RGPD)
     *
     * URL: GET /confidentialite
     *
     * @return string
     */
    public function confidentialite(): string
    {
        return view('Legal/confidentialite', [
            'title' => 'Politique de confidentialité'
        ]);
    }

    /**
     * Conditions générales de vente (CGV)
     *
     * URL: GET /cgv
     *
     * @return string
     */
    public function cgv(): string
    {
        return view('Legal/cgv', [
            'title' => 'Conditions générales de vente'
        ]);
    }

    /**
     * Mentions légales
     *
     * URL: GET /mentions-legales
     *
     * @return string
     */
    public function mentionsLegales(): string
    {
        return view('Legal/mentions-legales', [
            'title' => 'Mentions légales'
        ]);
    }
}
