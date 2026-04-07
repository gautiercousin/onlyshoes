<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Gestion des Signalements - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
</style>
<body class="bg-gray-50 min-h-screen overflow-x-hidden">
    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="lg:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-3 rounded-xl shadow-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <div class="flex overflow-x-hidden max-w-full">
        <?php 
        $current_page = 'signalements';
        echo view('components/admin_sidebar', ['current_page' => $current_page]); 
        ?>

        <!-- Main Content -->
        <main class="w-full lg:ml-64 flex-1 p-4 sm:p-6 lg:p-8 pt-20 lg:pt-8 overflow-x-hidden max-w-full">
            <?php 
            echo view('components/admin_header', [
                'page_title' => 'Gestion des Signalements',
                'page_subtitle' => 'Traiter les signalements (annonces, avis, comptes)',
                'admin_nom' => $admin_nom ?? 'Administrateur'
            ]); 
            ?>

            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-xl">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-green-700 font-medium"><?= session()->getFlashdata('success') ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="text-red-700 font-medium"><?= session()->getFlashdata('error') ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filtres par statut et type -->
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Filtres par statut -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Filtrer par statut</label>
                        <div class="flex flex-wrap gap-2">
                            <a href="<?= base_url('admin/signalements?statut=en_attente' . (isset($_GET['type']) ? '&type=' . $_GET['type'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($statut ?? 'en_attente') === 'en_attente' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                En attente
                            </a>
                            <a href="<?= base_url('admin/signalements?statut=traite' . (isset($_GET['type']) ? '&type=' . $_GET['type'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($statut ?? 'en_attente') === 'traite' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Traités
                            </a>
                            <a href="<?= base_url('admin/signalements?statut=rejete' . (isset($_GET['type']) ? '&type=' . $_GET['type'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($statut ?? 'en_attente') === 'rejete' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Rejetés
                            </a>
                            <a href="<?= base_url('admin/signalements?statut=tous' . (isset($_GET['type']) ? '&type=' . $_GET['type'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($statut ?? 'en_attente') === 'tous' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                Tous
                            </a>
                        </div>
                    </div>

                    <!-- Filtres par type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Filtrer par type</label>
                        <div class="flex flex-wrap gap-2">
                            <a href="<?= base_url('admin/signalements?type=tous' . (isset($_GET['statut']) ? '&statut=' . $_GET['statut'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($type ?? 'tous') === 'tous' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                Tous les types
                            </a>
                            <a href="<?= base_url('admin/signalements?type=annonce' . (isset($_GET['statut']) ? '&statut=' . $_GET['statut'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($type ?? 'tous') === 'annonce' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Annonces
                            </a>
                            <a href="<?= base_url('admin/signalements?type=user' . (isset($_GET['statut']) ? '&statut=' . $_GET['statut'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($type ?? 'tous') === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Comptes
                            </a>
                            <a href="<?= base_url('admin/signalements?type=review' . (isset($_GET['statut']) ? '&statut=' . $_GET['statut'] : '')) ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($type ?? 'tous') === 'review' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Avis
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Signalements</p>
                            <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?= count($signalements) ?></p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-2 sm:p-3">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Annonces</p>
                            <p class="text-2xl sm:text-3xl font-bold text-green-600">
                                <?= count(array_filter($signalements, fn($s) => $s['type'] === 'annonce')) ?>
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 sm:p-3">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Comptes & Avis</p>
                            <p class="text-2xl sm:text-3xl font-bold text-blue-600">
                                <?= count(array_filter($signalements, fn($s) => $s['type'] === 'user' || $s['type'] === 'review')) ?>
                            </p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 sm:p-3">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table des signalements -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Cible</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Motif</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Auteur</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden xl:table-cell">Date</th>
                                <?php if (($statut ?? 'en_attente') === 'traite' || ($statut ?? 'en_attente') === 'tous'): ?>
                                    <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden 2xl:table-cell">Décision</th>
                                <?php endif; ?>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($signalements)): ?>
                                <tr>
                                    <td colspan="<?= (($statut ?? 'en_attente') === 'traite' || ($statut ?? 'en_attente') === 'tous') ? '8' : '7' ?>" class="px-6 py-12 text-center">
                                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucun signalement</h3>
                                        <p class="text-gray-500">Aucun signalement ne correspond à vos critères de filtrage.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($signalements as $signalement): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <!-- Statut -->
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold
                                                <?php 
                                                    switch($signalement['statut']) {
                                                        case 'en_attente':
                                                            echo 'bg-orange-100 text-orange-700';
                                                            break;
                                                        case 'traite':
                                                            echo 'bg-green-100 text-green-700';
                                                            break;
                                                        case 'rejete':
                                                            echo 'bg-red-100 text-red-700';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-100 text-gray-700';
                                                    }
                                                ?>">
                                                <?php 
                                                    switch($signalement['statut']) {
                                                        case 'en_attente': echo 'En attente'; break;
                                                        case 'traite': echo 'Traité'; break;
                                                        case 'rejete': echo 'Rejeté'; break;
                                                        default: echo esc($signalement['statut']);
                                                    }
                                                ?>
                                            </span>
                                        </td>
                                        
                                        <!-- Type -->
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold
                                                <?php 
                                                    switch($signalement['type']) {
                                                        case 'user':
                                                            echo 'bg-blue-100 text-blue-700';
                                                            break;
                                                        case 'annonce':
                                                            echo 'bg-green-100 text-green-700';
                                                            break;
                                                        case 'review':
                                                            echo 'bg-yellow-100 text-yellow-700';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-100 text-gray-700';
                                                    }
                                                ?>">
                                                <?php 
                                                    switch($signalement['type']) {
                                                        case 'user':
                                                            echo '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Compte';
                                                            break;
                                                        case 'annonce':
                                                            echo '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Annonce';
                                                            break;
                                                        case 'review':
                                                            echo '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg> Avis';
                                                            break;
                                                        default:
                                                            echo esc($signalement['type']);
                                                    }
                                                ?>
                                            </span>
                                        </td>

                                        <!-- Cible -->
                                        <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($signalement['cible_nom'] ?? 'N/A') ?>
                                            </div>
                                        </td>

                                        <!-- Motif -->
                                        <td class="px-3 sm:px-6 py-4">
                                            <div class="text-sm text-gray-900 font-medium"><?= esc($signalement['motif']) ?></div>
                                            <?php if (!empty($signalement['description'])): ?>
                                                <div class="text-xs text-gray-500 mt-1 max-w-xs truncate">
                                                    <?= esc(substr($signalement['description'], 0, 60)) ?><?= strlen($signalement['description']) > 60 ? '...' : '' ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Auteur -->
                                        <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
                                            <div class="text-sm text-gray-900">
                                                <?= esc($signalement['auteur_prenom'] ?? '') ?> <?= esc($signalement['auteur_nom'] ?? '') ?>
                                            </div>
                                            <div class="text-xs text-gray-500"><?= esc($signalement['auteur_email'] ?? '') ?></div>
                                        </td>

                                        <!-- Date -->
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                                            <div class="text-sm text-gray-900">
                                                <?= date('d/m/Y', strtotime($signalement['date'])) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= date('H:i', strtotime($signalement['date'])) ?>
                                            </div>
                                        </td>

                                        <!-- Décision (visible seulement pour traité/tous) -->
                                        <?php if (($statut ?? 'en_attente') === 'traite' || ($statut ?? 'en_attente') === 'tous'): ?>
                                            <td class="px-3 sm:px-6 py-4 hidden 2xl:table-cell">
                                                <?php if ($signalement['statut'] === 'traite' && !empty($signalement['raison_decision'])): ?>
                                                    <div class="text-sm text-gray-900 max-w-xs">
                                                        <?= esc(substr($signalement['raison_decision'], 0, 80)) ?><?= strlen($signalement['raison_decision']) > 80 ? '...' : '' ?>
                                                    </div>
                                                    <?php if (!empty($signalement['date_traitement'])): ?>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            <?= date('d/m/Y H:i', strtotime($signalement['date_traitement'])) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-xs text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>

                                        <!-- Actions -->
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-center">
                                            <?php 
                                                // Construire l'URL de détail selon le type
                                                $detailUrl = '';
                                                switch($signalement['type']) {
                                                    case 'annonce':
                                                        $detailUrl = base_url('admin/signalement/annonce/' . $signalement['id_cible']);
                                                        break;
                                                    case 'review':
                                                        $detailUrl = base_url('admin/signalement/review/' . $signalement['id_cible']);
                                                        break;
                                                    case 'user':
                                                        $detailUrl = base_url('admin/signalement/compte/' . $signalement['id_cible']);
                                                        break;
                                                }
                                            ?>
                                            <a href="<?= $detailUrl ?>" 
                                               class="inline-flex items-center px-3 sm:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-medium rounded-lg transition shadow-sm">
                                                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span class="hidden sm:inline">Détail</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php 
            if (isset($pagination)) {
                echo view('components/pagination', [
                    'pagination' => $pagination,
                    'base_url' => base_url('admin/signalements')
                ]); 
            }
            ?>
        </main>
    </div>

    <script>
        // Toggle mobile menu
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.querySelector('aside');
        
        mobileMenuBtn?.addEventListener('click', () => {
            sidebar?.classList.toggle('-translate-x-full');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar?.contains(e.target) && !mobileMenuBtn?.contains(e.target)) {
                    sidebar?.classList.add('-translate-x-full');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar?.classList.remove('-translate-x-full');
            }
        });
    </script>
</body>
</html>
