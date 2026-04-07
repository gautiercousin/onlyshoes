<?php
$title = (isset($product['titre']) ? $product['titre'] : 'Produit') . ' - OnlyShoes';
$marque = $product['marque_nom'] ?? $product['marque'] ?? 'Marque inconnue';
?>

<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => $title]) ?>
<body class="bg-gray-50 overflow-x-hidden">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
        }
        
        .shoe-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .shoe-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
        }
    </style>

    <!-- Header -->
    <?= view('components/header') ?>

    <!-- le breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="/" class="hover:text-green-600 transition">Accueil</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="/recherche?brand=<?= urlencode($product['marque_nom'] ?? '') ?>" class="hover:text-green-600 transition"><?= esc($product['marque_nom'] ?? 'Marque') ?></a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-900 font-medium"><?= esc($product['titre'] ?? 'Produit') ?></span>
        </nav>
    </div>

    <!-- la section du produit -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="space-y-4">
                <!-- image principale du produit -->
                <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-3xl overflow-hidden flex items-center justify-center relative">
                    <img src="<?= $product['image_url'] ?? '/notfound.webp' ?>" 
                         alt="<?= esc($product['marque_nom']) ?> <?= esc($product['titre']) ?>" 
                         class="w-full h-full object-cover"
                         onerror="this.src='/notfound.webp'">
                </div>
            </div>

            <!-- les informations du produit -->
            <div class="space-y-6">
                <!-- marque et titre du produit -->
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-green-600 font-semibold text-sm uppercase tracking-wide"><?= esc($product['marque_nom'] ?? 'Marque') ?></p>
                        
                        <!-- Bouton de signalement discret -->
                        <?= view('components/report_button', [
                            'type' => 'annonce',
                            'id' => $product['id_annonce'],
                            'authorId' => $product['id_utilisateur_vendeur']
                        ]) ?>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4"><?= esc($product['titre']) ?></h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-4xl font-bold text-gray-900"><?= number_format($product['prix'], 2) ?>€</span>
                    </div>
                </div>

                <!-- les conditions et la pointure du produit -->
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">État :</span>
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full"><?= esc(ucfirst(str_replace('_', ' ', $product['etat']))) ?></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">Pointure :</span>
                        <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full"><?= esc($product['taille']) ?> <?= esc($product['taille_systeme']) ?></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">Couleur :</span>
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full"><?= esc($product['couleur_nom']) ?></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">Matériau :</span>
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full"><?= esc($product['materiau_nom']) ?></span>
                    </div>
                    <?php if ($product['taille_systeme']): ?>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">Système :</span>
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full"><?= esc($product['taille_systeme']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- les actions -->
                <div class="space-y-3 pt-4">
                    <?php if (session()->get('is_logged_in') && session()->get('user_id') === $product['id_utilisateur_vendeur']): ?>
                        <!-- Message pour le propriétaire du produit -->
                        <div class="w-full bg-gray-100 text-gray-600 font-semibold py-4 px-6 rounded-2xl flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Votre produit</span>
                        </div>
                    <?php else: ?>
                        <!-- Bouton d'achat pour les autres utilisateurs -->
                        <a href="/paiement/<?= esc($product['id_annonce']) ?>" class="w-full bg-green-600 text-white font-semibold py-4 px-6 rounded-2xl hover:bg-green-700 transition flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>ACHETER MAINTENANT</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- les informations du vendeur -->
                <a href="/utilisateur/profil/<?= $seller['id_utilisateur'] ?>" class="block bg-gray-50 rounded-2xl p-5 mt-6 hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-4">
                        <?= view('components/user-avatar', [
                            'prenom' => $seller['prenom'] ?? '',
                            'nom' => $seller['nom'] ?? '',
                            'size' => 'w-14 h-14',
                            'textSize' => 'text-xl',
                            'isAdmin' => ($seller['type_compte'] ?? '') === 'admin'
                        ]) ?>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-semibold text-gray-900"><?= esc($seller['prenom']) ?> <?= esc($seller['nom']) ?></h3>
                                <?php if ($seller['type_compte'] === 'admin'): ?>
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <span>Membre depuis <?= date('Y', strtotime($seller['date_creation'])) ?></span>
                            </div>
                            <p class="text-sm text-gray-500">Vendeur depuis <?= date('Y', strtotime($seller['date_creation'])) ?></p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <!-- la description du produit -->
                <div class="pt-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Description</h3>
                    <p class="text-gray-600 leading-relaxed">
                        <?= nl2br(esc($product['description'])) ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- les produits similaires -->
    <?php if (!empty($similarProducts)): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Produits similaires</h2>
                <p class="text-gray-600">Découvrez d'autres sneakers qui pourraient vous plaire</p>
            </div>
            <a href="/recherche" class="text-sm font-medium text-green-600 hover:underline">Tout voir</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($similarProducts as $similarProduct): ?>
                <?= view('components/product-card', ['product' => $similarProduct]) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- le footer -->
    <?= view('components/footer') ?>
</body>
</html>
