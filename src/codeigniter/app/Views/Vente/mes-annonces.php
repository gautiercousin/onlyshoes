<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Mes annonces - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.3);
    }

    .product-card:hover .product-image {
        transform: scale(1.08);
    }

    .product-image {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .badge-pulse {
        animation: pulse-badge 2s ease-in-out infinite;
    }

    @keyframes pulse-badge {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(16, 185, 129, 0.25);
    }

    .action-btn {
        transition: all 0.2s ease;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.2);
    }

    .empty-state-icon {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-15px);
        }
    }

    .price-tag {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<body class="bg-gradient-to-br from-gray-50 to-green-50 min-h-screen">
    <?= view('components/header') ?>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <!-- Page Header -->
            <div class="glass-effect rounded-2xl shadow-lg p-8 mb-8 border border-white/50">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes annonces</h1>
                        <p class="text-gray-600">Gérez et suivez vos produits en vente</p>
                    </div>
                    <a href="/vendre"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Nouvelle annonce</span>
                    </a>
                </div>

                <!-- Stats Cards -->
                <?php if (!empty($annonces)): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8 pt-8 border-t border-gray-200">
                    <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Total</p>
                                <p class="text-3xl font-bold text-gray-900"><?= count($annonces) ?></p>
                            </div>
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">En vente</p>
                                <p class="text-3xl font-bold text-gray-900">
                                    <?= count(array_filter($annonces, fn($a) => $a['disponible'])) ?>
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Valeur totale</p>
                                <p class="text-3xl font-bold text-gray-900">
                                    <?= number_format(array_sum(array_column($annonces, 'prix')), 0, ',', ' ') ?>€
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="glass-effect rounded-xl shadow-lg p-5 mb-6 border-l-4 border-green-500">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-green-800 font-semibold"><?= session()->getFlashdata('success') ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="glass-effect rounded-xl shadow-lg p-5 mb-6 border-l-4 border-red-500">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <p class="text-red-800 font-semibold"><?= session()->getFlashdata('error') ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Content -->
            <?php if (empty($annonces)): ?>
                <!-- Empty State -->
                <div class="glass-effect rounded-2xl shadow-lg p-16 text-center border border-white/50">
                    <div class="empty-state-icon inline-block mb-6">
                        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Aucune annonce</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Commencez à vendre vos sneakers dès maintenant
                    </p>
                    <a href="/vendre"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Créer ma première annonce</span>
                    </a>
                </div>
            <?php else: ?>
                <!-- Products Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($annonces as $annonce): ?>
                        <div class="product-card glass-effect rounded-2xl shadow-lg overflow-hidden border border-white/50 group">

                            <!-- Product Image -->
                            <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                <img src="<?= esc($annonce['image_url'] ?? '/notfound.webp') ?>"
                                     alt="<?= esc($annonce['titre']) ?>"
                                     class="product-image w-full h-full object-cover"
                                     onerror="this.src='/notfound.webp'">

                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3">
                                    <?php if ($annonce['disponible']): ?>
                                        <span class="badge-pulse inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                                            <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                                            En vente
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 text-white text-xs font-bold rounded-full shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Vendu
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Dark overlay on hover -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-5">
                                <!-- Title -->
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 min-h-[56px]">
                                    <?= esc($annonce['titre']) ?>
                                </h3>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[40px]">
                                    <?= esc($annonce['description']) ?>
                                </p>

                                <!-- Price & Size Row -->
                                <div class="flex items-end justify-between mb-5 pb-5 border-b border-gray-200">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Prix</p>
                                        <p class="text-2xl font-bold price-tag">
                                            <?= number_format($annonce['prix'], 2, ',', ' ') ?>€
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 mb-1">Taille</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            <?= esc($annonce['taille']) ?> <span class="text-sm text-gray-500"><?= esc($annonce['taille_systeme']) ?></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div class="flex items-center gap-2 mb-5">
                                    <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-semibold rounded-lg border border-green-200">
                                        <?= esc($annonce['marque_nom'] ?? 'N/A') ?>
                                    </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg border border-gray-200">
                                        <?= esc(ucfirst(str_replace('_', ' ', $annonce['etat']))) ?>
                                    </span>
                                </div>

                                <!-- Date -->
                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Publié le <?= date('d/m/Y', strtotime($annonce['date_publication'])) ?></span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-3 gap-2">
                                    <!-- View Button -->
                                    <a href="/produit/<?= esc($annonce['id_annonce']) ?>"
                                       class="action-btn bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="/modifier-annonce/<?= esc($annonce['id_annonce']) ?>"
                                       class="action-btn bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="/supprimer-annonce/<?= esc($annonce['id_annonce']) ?>"
                                          method="POST"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?\n\nCette action est irréversible.')">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                                class="action-btn w-full bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 shadow-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="mt-8 flex justify-center">
                        <div class="glass-effect rounded-xl shadow-lg p-4 border border-white/50">
                            <nav class="flex items-center gap-2">
                                <!-- Previous Button -->
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <a href="?page=<?= $pagination['current_page'] - 1 ?>"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Précédent
                                    </a>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <div class="flex items-center gap-1 px-2">
                                    <?php
                                    $range = 2;
                                    $start = max(1, $pagination['current_page'] - $range);
                                    $end = min($pagination['total_pages'], $pagination['current_page'] + $range);

                                    if ($start > 1): ?>
                                        <a href="?page=1"
                                           class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-green-500 hover:text-white hover:border-green-500 transition-all">
                                            1
                                        </a>
                                        <?php if ($start > 2): ?>
                                            <span class="text-gray-400 px-2">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $start; $i <= $end; $i++): ?>
                                        <?php if ($i == $pagination['current_page']): ?>
                                            <span class="w-10 h-10 flex items-center justify-center bg-green-500 text-white font-bold rounded-lg shadow-md">
                                                <?= $i ?>
                                            </span>
                                        <?php else: ?>
                                            <a href="?page=<?= $i ?>"
                                               class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-green-500 hover:text-white hover:border-green-500 transition-all">
                                                <?= $i ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <?php if ($end < $pagination['total_pages']): ?>
                                        <?php if ($end < $pagination['total_pages'] - 1): ?>
                                            <span class="text-gray-400 px-2">...</span>
                                        <?php endif; ?>
                                        <a href="?page=<?= $pagination['total_pages'] ?>"
                                           class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-green-500 hover:text-white hover:border-green-500 transition-all">
                                            <?= $pagination['total_pages'] ?>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Next Button -->
                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                    <a href="?page=<?= $pagination['current_page'] + 1 ?>"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all">
                                        Suivant
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </nav>

                            <!-- Results Info -->
                            <div class="text-center mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600">
                                    Page <span class="font-bold text-green-600"><?= $pagination['current_page'] ?></span>
                                    sur <span class="font-bold"><?= $pagination['total_pages'] ?></span>
                                    <span class="mx-2">•</span>
                                    <span class="font-bold"><?= $pagination['total_items'] ?></span> annonce<?= $pagination['total_items'] > 1 ? 's' : '' ?> au total
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?= view('components/footer') ?>
</body>
</html>
