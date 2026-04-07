<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Détails Avis Signalé - Admin OnlyShoes']) ?>
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
                'page_title' => 'Détails de l\'avis signalé',
                'page_subtitle' => 'Modération des avis (Exigence 33)',
                'admin_nom' => $admin_nom ?? 'Administrateur'
            ]); 
            ?>

            <!-- Header avec retour -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
                <a href="<?= base_url('admin/signalements?type=review') ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour aux signalements d'avis
                </a>
                <span class="hidden sm:inline text-gray-400">|</span>
                <a href="<?= base_url('admin/signalements') ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                    Tous les signalements
                </a>
            </div>

            <!-- Messages Flash -->
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

            <!-- Informations de l'avis -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <!-- Carte principale de l'avis -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">
                                Avis #<?= esc(substr($review['id_review'], 0, 8)) ?>
                            </h2>
                            <div class="flex items-center space-x-2 mb-3">
                                <!-- Affichage de la note -->
                                <div class="flex items-center">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $review['note']): ?>
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-lg font-bold text-gray-900"><?= esc($review['note']) ?>/5</span>
                            </div>
                            <p class="text-sm text-gray-500">
                                Publié le <?= date('d/m/Y à H:i', strtotime($review['date_publication'])) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Commentaire de l'avis -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            Commentaire
                        </h3>
                        <p class="text-gray-900 whitespace-pre-wrap"><?= esc($review['commentaire']) ?></p>
                    </div>

                    <!-- Informations Auteur et Vendeur -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Auteur de l'avis -->
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-200">
                            <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Auteur de l'avis
                            </h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs text-blue-600 mb-1">Nom</p>
                                    <p class="font-medium text-blue-900">
                                        <?= esc($review['auteur_prenom']) ?> <?= esc($review['auteur_nom']) ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600 mb-1">Email</p>
                                    <p class="text-sm text-blue-900"><?= esc($review['auteur_email']) ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600 mb-1">Statut du compte</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        <?= $review['auteur_status'] === 'actif' ? 'bg-green-100 text-green-800' : 
                                            ($review['auteur_status'] === 'suspendu' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                        <?= esc(ucfirst($review['auteur_status'])) ?>
                                    </span>
                                </div>
                                <a href="<?= base_url('admin/signalement/compte/' . $review['auteur_id']) ?>" 
                                   class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 font-medium mt-2">
                                    Voir le profil complet
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Vendeur concerné -->
                        <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                            <h3 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Vendeur concerné
                            </h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs text-green-600 mb-1">Nom</p>
                                    <p class="font-medium text-green-900">
                                        <?= esc($review['vendeur_prenom']) ?> <?= esc($review['vendeur_nom']) ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-green-600 mb-1">Email</p>
                                    <p class="text-sm text-green-900"><?= esc($review['vendeur_email']) ?></p>
                                </div>
                                <a href="<?= base_url('admin/signalement/compte/' . $review['vendeur_id']) ?>" 
                                   class="inline-flex items-center text-xs text-green-600 hover:text-green-800 font-medium mt-2">
                                    Voir le profil complet
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte statistiques -->
                <div class="space-y-4">
                    <!-- Nombre de signalements -->
                    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Signalements</p>
                                <p class="text-2xl sm:text-3xl font-bold text-orange-600">
                                    <?= count($review['signalements']) ?>
                                </p>
                            </div>
                            <div class="bg-orange-100 rounded-full p-2 sm:p-3">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <?= count(array_filter($review['signalements'], fn($s) => $s['statut'] === 'en_attente')) ?> en attente
                        </p>
                    </div>

                    <!-- Actions administrateur -->
                    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Actions
                        </h3>
                        <div class="space-y-3">
                            <!-- Bouton Supprimer l'avis -->
                            <button onclick="openDeleteModal()" 
                                    class="w-full flex items-center justify-center px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer l'avis
                            </button>

                            <!-- Bouton Rejeter les signalements -->
                            <button onclick="openRejectModal()" 
                                    class="w-full flex items-center justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Conserver l'avis
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des signalements -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Détails des signalements (<?= count($review['signalements']) ?>)
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Motif</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Signalé par</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($review['signalements'])): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Aucun signalement pour cet avis
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($review['signalements'] as $signalement): ?>
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

                                        <!-- Motif -->
                                        <td class="px-3 sm:px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($signalement['motif']) ?></div>
                                            <?php if (!empty($signalement['description'])): ?>
                                                <div class="text-xs text-gray-500 mt-1 max-w-md">
                                                    <?= esc($signalement['description']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($signalement['raison_decision'])): ?>
                                                <div class="text-xs text-blue-600 mt-2 italic">
                                                    Décision : <?= esc($signalement['raison_decision']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Signalé par -->
                                        <td class="px-3 sm:px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                <?= esc($signalement['signaleur_prenom']) ?> <?= esc($signalement['signaleur_nom']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500"><?= esc($signalement['signaleur_email']) ?></div>
                                        </td>

                                        <!-- Date -->
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                            <div class="text-sm text-gray-900">
                                                <?= date('d/m/Y', strtotime($signalement['date'])) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= date('H:i', strtotime($signalement['date'])) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-start mb-4">
                <div class="bg-red-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Supprimer cet avis ?</h3>
                    <p class="text-sm text-gray-600">
                        Cette action est irréversible. L'avis sera définitivement supprimé et tous les signalements associés seront marqués comme traités.
                    </p>
                </div>
            </div>

            <form action="<?= base_url('admin/supprimerReview') ?>" method="POST">
                <input type="hidden" name="id_review" value="<?= esc($review['id_review']) ?>">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Raison de la suppression (optionnel)
                    </label>
                    <textarea 
                        name="raison" 
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Ex: Contenu inapproprié, violation des CGU..."
                    ></textarea>
                </div>

                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium"
                    >
                        Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmation de rejet -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-start mb-4">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Conserver cet avis ?</h3>
                    <p class="text-sm text-gray-600">
                        Les signalements seront marqués comme rejetés et l'avis sera conservé sur la plateforme.
                    </p>
                </div>
            </div>

            <form action="<?= base_url('admin/rejeterSignalementsReview') ?>" method="POST">
                <input type="hidden" name="id_review" value="<?= esc($review['id_review']) ?>">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Raison du rejet (optionnel)
                    </label>
                    <textarea 
                        name="raison" 
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Ex: Avis conforme aux règles, signalements non justifiés..."
                    ></textarea>
                </div>

                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeRejectModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium"
                    >
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
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

        // Modal functions
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Close modals on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeRejectModal();
            }
        });

        // Close modal on outside click
        document.getElementById('deleteModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'deleteModal') {
                closeDeleteModal();
            }
        });

        document.getElementById('rejectModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'rejectModal') {
                closeRejectModal();
            }
        });
    </script>
</body>
</html>
