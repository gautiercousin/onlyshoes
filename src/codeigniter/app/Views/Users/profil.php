<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => ($user['prenom'] ?? 'Utilisateur') . ' ' . ($user['nom'] ?? '') . ' - OnlyShoes']) ?>
<body class="bg-gray-50 overflow-x-hidden">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
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

    <?= view('components/header') ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <!-- Header utilisateur -->
        <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Avatar -->
                <?= view('components/user-avatar', [
                    'prenom' => $user['prenom'] ?? '',
                    'nom' => $user['nom'] ?? '',
                    'size' => 'w-24 h-24',
                    'textSize' => 'text-3xl',
                    'extraClasses' => 'flex-shrink-0',
                    'isAdmin' => ($user['type_compte'] ?? '') === 'admin'
                ]) ?>

                <!-- Infos -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900">
                            <?= esc($user['prenom'] ?? '') ?> <?= esc($user['nom'] ?? '') ?>
                        </h1>

                        <!-- Badge admin -->
                        <?php if (($user['type_compte'] ?? 'standard') === 'admin'): ?>
                        <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                            Admin
                        </span>
                        <?php endif; ?>

                        <!-- Bouton signaler -->
                        <?= view('components/report_button', [
                            'type' => 'user',
                            'id' => $user['id_utilisateur'] ?? '',
                            'position' => 'right'
                        ]) ?>
                    </div>

                    <p class="text-gray-500 mb-4"><?= esc($user['email'] ?? '') ?></p>

                    <!-- Statistiques -->
                    <div class="flex flex-wrap gap-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold text-gray-900"><?= $pagination['total_items'] ?></span>
                            <span class="text-gray-500">Annonce<?= $pagination['total_items'] > 1 ? 's' : '' ?></span>
                        </div>

                        <?php if ($totalReviews > 0): ?>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="font-semibold text-gray-900"><?= $averageRating ?></span>
                            <span class="text-gray-500">(<?= $totalReviews ?> avis)</span>
                        </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-gray-500">
                                <?= ($user['type_compte'] ?? 'standard') === 'admin' ? 'Administrateur' : 'Membre' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Annonces de l'utilisateur -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    <?php if ($pagination['total_items'] > 0): ?>
                        Annonces de <?= esc($user['prenom'] ?? 'cet utilisateur') ?>
                    <?php else: ?>
                        Profil de <?= esc($user['prenom'] ?? 'cet utilisateur') ?>
                    <?php endif; ?>
                </h2>
                <span class="text-gray-500"><?= $pagination['total_items'] ?> article<?= $pagination['total_items'] > 1 ? 's' : '' ?></span>
            </div>

            <?php if ($pagination['total_items'] > 0): ?>
            <!-- Grille de produits -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($userAnnonces as $annonce): ?>
                    <?= view('components/product-card', ['product' => $annonce]) ?>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?= view('components/pagination', ['pagination' => $pagination]) ?>

            <?php else: ?>
            <!-- Message si pas d'annonces -->
            <div class="bg-white rounded-3xl p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune annonce</h3>
                <p class="text-gray-500">
                    <?php if (session('user_id') === $user['id_utilisateur']): ?>
                        Vous n'avez pas encore publié d'annonces.
                    <?php else: ?>
                        Cet utilisateur n'a pas encore d'annonces en ligne.
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Section des avis -->
        <div class="mb-8" id="reviews">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Avis clients</h2>
                    <?php if ($totalReviews > 0): ?>
                        <p class="text-sm text-gray-500 mt-1">
                            <?= $totalReviews ?> avis • Note moyenne: <?= $averageRating ?>/5
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Action button for logged-in users who purchased -->
                <?php if ($hasPurchased && session()->get('is_logged_in')): ?>
                    <?php if ($userReview): ?>
                        <a href="<?= base_url('review/modifier/' . $userReview['id_review']) ?>?redirect=<?= urlencode(current_url()) ?>"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Modifier mon avis
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('review/creer/' . $user['id_utilisateur']) ?>?redirect=<?= urlencode(current_url()) ?>"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Laisser un avis
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ($totalReviews > 0): ?>
                <div class="space-y-4">
                    <!-- User's own review (highlighted at top) -->
                    <?php if ($userReview): ?>
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl shadow-sm p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <?= view('components/user-avatar', [
                                        'prenom' => session()->get('user_prenom') ?? '',
                                        'nom' => session()->get('user_nom') ?? '',
                                        'size' => 'w-10 h-10',
                                        'textSize' => 'text-sm'
                                    ]) ?>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-gray-900">Votre avis</p>
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Vous</span>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            <?= date('d/m/Y', strtotime($userReview['date'])) ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="flex items-center gap-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <svg class="w-5 h-5 <?= $i <= $userReview['note'] ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <?php if (!empty($userReview['commentaire'])): ?>
                                <p class="text-gray-700 leading-relaxed">
                                    <?= esc($userReview['commentaire']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Other reviews (paginated) -->
                    <?php foreach ($reviews as $review): ?>
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <?php if (isset($review['auteur_id'])): ?>
                                        <a href="<?= base_url('utilisateur/profil/' . $review['auteur_id']) ?>" class="flex-shrink-0">
                                            <?= view('components/user-avatar', [
                                                'prenom' => $review['auteur_prenom'] ?? '',
                                                'nom' => $review['auteur_nom'] ?? '',
                                                'size' => 'w-10 h-10',
                                                'textSize' => 'text-sm'
                                            ]) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= view('components/user-avatar', [
                                            'prenom' => $review['auteur_prenom'] ?? '',
                                            'nom' => $review['auteur_nom'] ?? '',
                                            'size' => 'w-10 h-10',
                                            'textSize' => 'text-sm'
                                        ]) ?>
                                    <?php endif; ?>
                                    <div>
                                        <?php if (isset($review['auteur_id'])): ?>
                                            <a href="<?= base_url('utilisateur/profil/' . $review['auteur_id']) ?>" class="font-semibold text-gray-900 hover:text-green-600 transition">
                                                <?= esc($review['auteur_prenom']) ?> <?= esc($review['auteur_nom']) ?>
                                            </a>
                                        <?php else: ?>
                                            <p class="font-semibold text-gray-900">
                                                <?= esc($review['auteur_prenom']) ?> <?= esc($review['auteur_nom']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-xs text-gray-500">
                                            <?= date('d/m/Y', strtotime($review['date_review'])) ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Note and Report Button -->
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <svg class="w-5 h-5 <?= $i <= $review['note'] ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                    <?= view('components/report_button', [
                                        'type' => 'review',
                                        'id' => $review['id_review'],
                                        'authorId' => $review['auteur_id'],
                                        'position' => 'right'
                                    ]) ?>
                                </div>
                            </div>

                            <?php if (!empty($review['commentaire'])): ?>
                                <p class="text-gray-700 leading-relaxed">
                                    <?= esc($review['commentaire']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Review pagination -->
                    <?php if ($reviewPagination['total_pages'] > 1): ?>
                        <?= view('components/pagination', [
                            'pagination' => $reviewPagination,
                            'paramName' => 'review_page',
                            'anchor' => '#reviews'
                        ]) ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- No reviews yet -->
                <div class="bg-white rounded-2xl p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun avis pour le moment</h3>
                    <p class="text-gray-500">
                        <?php if ($hasPurchased): ?>
                            Soyez le premier à laisser un avis sur ce vendeur!
                        <?php else: ?>
                            Achetez un produit pour pouvoir laisser un avis.
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?= view('components/footer') ?>
</body>
</html>
