<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/produit/(:any)', 'Product::show/$1');
$routes->get('/paiement/(:any)', 'Paiement::show/$1');
$routes->post('/paiement/(:any)', 'Paiement::process/$1');

// Routes pour l'authentification
$routes->get('/connexion', 'Auth::connexion');
$routes->post('/connexion', 'Auth::doConnexion');
$routes->get('/inscription', 'Auth::inscription');
$routes->post('/inscription', 'Auth::doInscription');
$routes->get('/deconnexion', 'Auth::deconnexion');

// Route pour la recherche de produits
$routes->get('/recherche', 'Search::index');

// Routes pour vendre un produit
$routes->get('/vendre', 'Vendre::index');
$routes->post('/vendre', 'Vendre::publier');

// Routes pour gérer ses annonces
$routes->get('/mes-annonces', 'Vendre::mesAnnonces');
$routes->get('/modifier-annonce/(:any)', 'Vendre::modifier/$1');
$routes->post('/modifier-annonce/(:any)', 'Vendre::updateAnnonce/$1');
$routes->post('/supprimer-annonce/(:any)', 'Vendre::supprimerAnnonce/$1');

// Route pour le profil utilisateur
$routes->get('/utilisateur/profil/(:any)', 'Utilisateur::profil/$1');
// Route pour la gestion du compte
$routes->get('/compte', 'Utilisateur::compte');
$routes->post('/compte', 'Utilisateur::updateCompte');
// Route pour les commandes utilisateur
$routes->get('/commandes', 'Utilisateur::commandes');
// Route pour les ventes utilisateur
$routes->get('/ventes', 'Utilisateur::ventes');
$routes->post('/ventes/update-statut', 'Utilisateur::updateStatutCommande');

// Route pour le vendeur (redirection vers profil)
$routes->get('/vendeur/(:any)', 'Seller::show/$1');

// Routes pour les signalements
$routes->get('/signalement/confirmation', 'Signalement::confirmation');
$routes->post('/signalement/create', 'Signalement::create');
$routes->get('/signalement/(:segment)/(:segment)', 'Signalement::show/$1/$2');

// Routes pour les avis (reviews)
$routes->get('/review/creer/(:any)', 'Review::creer/$1');
$routes->post('/review/store', 'Review::store');
$routes->get('/review/modifier/(:segment)', 'Review::modifier/$1');
$routes->post('/review/update/(:segment)', 'Review::update/$1');
$routes->post('/review/supprimer/(:segment)', 'Review::supprimer/$1');

// Routes pour la gestion des cookies (RGPD)
$routes->post('/cookies/save', 'CookieConsent::save');
$routes->post('/cookies/withdraw', 'CookieConsent::withdraw');
$routes->get('/cookies/preferences', 'CookieConsent::preferences');

// Routes pour les pages légales
$routes->get('/confidentialite', 'Legal::confidentialite');
$routes->get('/cgv', 'Legal::cgv');
$routes->get('/mentions-legales', 'Legal::mentionsLegales');

// Routes pour l'administration
$routes->get('/admin', 'Admin::index');
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->post('/admin/logout', 'Admin::logout');
$routes->get('/admin/signalements', 'Admin::signalements');
$routes->post('/admin/traiter-signalement', 'Admin::traiterSignalement');

// Routes pour la gestion des utilisateurs (Admin)
$routes->get('/admin/utilisateurs', 'Admin::utilisateurs');
$routes->get('/admin/utilisateur/(:any)', 'Admin::detailUtilisateur/$1');
$routes->post('/admin/utilisateur/suspendre', 'Admin::suspendreUtilisateur');
$routes->post('/admin/utilisateur/bannir', 'Admin::bannirUtilisateur');
$routes->post('/admin/utilisateur/reactiver', 'Admin::reactiverUtilisateur');
$routes->post('/admin/utilisateur/supprimer', 'Admin::supprimerUtilisateur');

// Routes pour la gestion des signalements (Admin) - Tous types
$routes->get('/admin/signalement/annonce/(:any)', 'Admin::detailSignalementAnnonce/$1');
$routes->get('/admin/signalement/review/(:any)', 'Admin::detailSignalementReview/$1');
$routes->get('/admin/signalement/compte/(:any)', 'Admin::detailSignalementCompte/$1');
$routes->post('/admin/supprimer-annonce', 'Admin::supprimerAnnonce');
$routes->post('/admin/rejeter-signalements-annonce', 'Admin::rejeterSignalementsAnnonce');
$routes->post('/admin/supprimerReview', 'Admin::supprimerReview');
$routes->post('/admin/rejeterSignalementsReview', 'Admin::rejeterSignalementsReview');

// Routes pour la gestion des attributs produits (Admin)
$routes->get('/admin/attributs', 'Admin::attributs');
$routes->post('/admin/ajouterMarque', 'Admin::ajouterMarque');
$routes->post('/admin/ajouterCouleur', 'Admin::ajouterCouleur');
$routes->post('/admin/ajouterMateriau', 'Admin::ajouterMateriau');
$routes->post('/admin/supprimerMarque', 'Admin::supprimerMarque');
$routes->post('/admin/supprimerCouleur', 'Admin::supprimerCouleur');
$routes->post('/admin/supprimerMateriau', 'Admin::supprimerMateriau');


