<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Détails Utilisateur - Admin OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
</style>
<body class="bg-gray-50 min-h-screen overflow-x-hidden">
    <div class="flex overflow-x-hidden max-w-full">
        <?php 
        $current_page = 'utilisateurs';
        echo view('components/admin_sidebar', ['current_page' => $current_page]); 
        ?>

        <!-- Main Content -->
        <main class="w-full lg:ml-64 flex-1 p-4 sm:p-6 lg:p-8 pt-20 lg:pt-8 overflow-x-hidden max-w-full">
            <!-- Header avec retour -->
            <div class="mb-6 flex items-center space-x-3">
                <a href="/admin/utilisateurs" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à la liste des utilisateurs
                </a>
                <span class="text-gray-400">|</span>
                <a href="/admin/signalements?type=user" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    Voir les signalements de comptes
                </a>
                
                <?php 
                echo view('components/admin_header', [
                    'page_title' => 'Détails de l\'utilisateur',
                    'page_subtitle' => 'Informations complètes et signalements',
                    'admin_nom' => $admin_nom ?? 'Administrateur'
                ]); 
                ?>
            </div>

            <!-- Messages Flash -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6">
                    <p class="font-medium"><?= session()->getFlashdata('success') ?></p>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6">
                    <p class="font-medium"><?= session()->getFlashdata('error') ?></p>
                </div>
            <?php endif; ?>

            <!-- Informations Utilisateur -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Carte principale utilisateur -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-100">
                                <img src="https://api.dicebear.com/9.x/thumbs/svg?seed=<?= urlencode($utilisateur['id_utilisateur'] ?? ($utilisateur['email'] ?? 'default')) ?>" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    <?= esc($utilisateur['prenom']) ?> <?= esc($utilisateur['nom']) ?>
                                </h2>
                                <p class="text-gray-600"><?= esc($utilisateur['email']) ?></p>
                                <p class="text-xs text-gray-400 mt-1">ID: <?= esc($utilisateur['id_utilisateur']) ?></p>
                            </div>
                        </div>
                        
                        <!-- Badge Status -->
                        <div>
                            <?php if ($utilisateur['status'] === 'actif'): ?>
                                <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ Actif
                                </span>
                            <?php elseif ($utilisateur['status'] === 'suspendu'): ?>
                                <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    ⏸ Suspendu
                                </span>
                            <?php else: ?>
                                <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    ✖ Bannis
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Informations détaillées -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Type de compte</p>
                            <?php if ($utilisateur['type_compte'] === 'admin'): ?>
                                <p class="font-medium text-purple-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Administrateur
                                </p>
                            <?php else: ?>
                                <p class="font-medium text-gray-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Standard
                                </p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date d'inscription</p>
                            <p class="font-medium text-gray-700">
                                <?= date('d/m/Y à H:i', strtotime($utilisateur['date_creation'])) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Téléphone</p>
                            <p class="font-medium text-gray-700"><?= esc($utilisateur['telephone'] ?? 'Non renseigné') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nombre d'annonces</p>
                            <p class="font-medium text-gray-700"><?= count($utilisateur['annonces'] ?? []) ?> annonce(s)</p>
                        </div>
                    </div>
                </div>

                <!-- Carte statistiques -->
                <div class="space-y-4">
                    <!-- Signalements -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Signalements</p>
                                <p class="text-3xl font-bold text-orange-600">
                                    <?= count($utilisateur['signalements'] ?? []) ?>
                                </p>
                            </div>
                            <div class="bg-orange-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <?= count(array_filter($utilisateur['signalements'] ?? [], fn($s) => $s['statut'] === 'en_attente')) ?> en attente
                        </p>
                    </div>

                    <!-- Annonces -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Annonces</p>
                                <p class="text-3xl font-bold text-green-600">
                                    <?= count($utilisateur['annonces'] ?? []) ?>
                                </p>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions de Modération -->
            <?php if ($utilisateur['type_compte'] !== 'admin'): ?>
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Actions de modération</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php if ($utilisateur['status'] === 'actif'): ?>
                            <button 
                                onclick="openSuspendModal('<?= $utilisateur['id_utilisateur'] ?>', '<?= esc($utilisateur['prenom']) ?> <?= esc($utilisateur['nom']) ?>')"
                                class="px-6 py-3 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition font-medium flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Suspendre le compte</span>
                            </button>
                        <?php endif; ?>

                        <?php if ($utilisateur['status'] !== 'bannis'): ?>
                            <button 
                                onclick="openBanModal('<?= $utilisateur['id_utilisateur'] ?>', '<?= esc($utilisateur['prenom']) ?> <?= esc($utilisateur['nom']) ?>')"
                                class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                <span>Bannir le compte</span>
                            </button>
                        <?php endif; ?>

                        <?php if ($utilisateur['status'] === 'suspendu' || $utilisateur['status'] === 'bannis'): ?>
                            <button 
                                onclick="openReactivateModal('<?= $utilisateur['id_utilisateur'] ?>', '<?= esc($utilisateur['prenom']) ?> <?= esc($utilisateur['nom']) ?>')"
                                class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Réactiver le compte</span>
                            </button>
                        <?php endif; ?>

                        <button 
                            onclick="openDeleteModal('<?= $utilisateur['id_utilisateur'] ?>', '<?= esc($utilisateur['prenom']) ?> <?= esc($utilisateur['nom']) ?>')"
                            class="px-6 py-3 bg-gray-700 text-white rounded-xl hover:bg-gray-800 transition font-medium flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Supprimer le compte</span>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-6 mb-6">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-purple-800 font-medium">Ce compte est un administrateur et ne peut pas être modéré.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Liste des Signalements -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Signalements (<?= count($utilisateur['signalements'] ?? []) ?>)</h3>
                </div>
                
                <?php if (empty($utilisateur['signalements'])): ?>
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucun signalement</h3>
                        <p class="text-gray-500">Cet utilisateur n'a pas été signalé.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($utilisateur['signalements'] as $signalement): ?>
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="font-semibold text-gray-900"><?= esc($signalement['motif']) ?></h4>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
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
                                        </div>
                                        <?php if (!empty($signalement['description'])): ?>
                                            <p class="text-sm text-gray-600 mb-2"><?= esc($signalement['description']) ?></p>
                                        <?php endif; ?>
                                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                                            <span>
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                Signalé par: <?= esc($signalement['auteur_prenom']) ?> <?= esc($signalement['auteur_nom']) ?>
                                            </span>
                                            <span>
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <?= date('d/m/Y à H:i', strtotime($signalement['date'])) ?>
                                            </span>
                                        </div>
                                        
                                        <?php if (!empty($signalement['raison_decision'])): ?>
                                            <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs font-medium text-gray-700 mb-1">Décision de l'admin:</p>
                                                <p class="text-sm text-gray-600"><?= esc($signalement['raison_decision']) ?></p>
                                                <?php if (!empty($signalement['date_traitement'])): ?>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Le <?= date('d/m/Y à H:i', strtotime($signalement['date_traitement'])) ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Annonces récentes -->
            <?php if (!empty($utilisateur['annonces'])): ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Annonces récentes (<?= count($utilisateur['annonces']) ?>)</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($utilisateur['annonces'] as $annonce): ?>
                            <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900"><?= esc($annonce['titre']) ?></h4>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="text-sm font-semibold text-green-600"><?= number_format($annonce['prix'], 2) ?> €</span>
                                        <span class="text-xs text-gray-500">
                                            <?= date('d/m/Y', strtotime($annonce['date_publication'])) ?>
                                        </span>
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full <?= $annonce['disponible'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' ?>">
                                            <?= $annonce['disponible'] ? 'Disponible' : 'Indisponible' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal Suspendre -->
    <div id="suspendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Suspendre l'utilisateur</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir suspendre <strong id="suspendUserName"></strong> ?</p>
            <form action="/admin/utilisateur/suspendre" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_utilisateur" id="suspendUserId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raison de la suspension</label>
                    <textarea name="raison" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500" placeholder="Expliquez pourquoi..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition">
                        Confirmer la suspension
                    </button>
                    <button type="button" onclick="closeSuspendModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Bannir -->
    <div id="banModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Bannir l'utilisateur</h3>
            <p class="text-gray-600 mb-6">⚠️ Cette action est grave. Êtes-vous sûr de vouloir bannir définitivement <strong id="banUserName"></strong> ?</p>
            <form action="/admin/utilisateur/bannir" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_utilisateur" id="banUserId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raison du bannissement</label>
                    <textarea name="raison" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500" placeholder="Expliquez pourquoi..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                        Confirmer le bannissement
                    </button>
                    <button type="button" onclick="closeBanModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Réactiver -->
    <div id="reactivateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold text-green-600 mb-4">Réactiver l'utilisateur</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir réactiver <strong id="reactivateUserName"></strong> ?</p>
            <form action="/admin/utilisateur/reactiver" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_utilisateur" id="reactivateUserId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raison de la réactivation (optionnel)</label>
                    <textarea name="raison" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500" placeholder="Expliquez pourquoi..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">
                        Confirmer la réactivation
                    </button>
                    <button type="button" onclick="closeReactivateModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Supprimer -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Supprimer l'utilisateur</h3>
            <p class="text-gray-600 mb-6">⚠️ <strong>ATTENTION!</strong> Cette action est irréversible. Toutes les données de <strong id="deleteUserName"></strong> seront définitivement supprimées.</p>
            <form action="/admin/utilisateur/supprimer" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_utilisateur" id="deleteUserId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raison de la suppression</label>
                    <textarea name="raison" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500" placeholder="Expliquez pourquoi..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                        Supprimer définitivement
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Suspendre
        function openSuspendModal(userId, userName) {
            document.getElementById('suspendUserId').value = userId;
            document.getElementById('suspendUserName').textContent = userName;
            document.getElementById('suspendModal').classList.remove('hidden');
        }
        function closeSuspendModal() {
            document.getElementById('suspendModal').classList.add('hidden');
        }

        // Modal Bannir
        function openBanModal(userId, userName) {
            document.getElementById('banUserId').value = userId;
            document.getElementById('banUserName').textContent = userName;
            document.getElementById('banModal').classList.remove('hidden');
        }
        function closeBanModal() {
            document.getElementById('banModal').classList.add('hidden');
        }

        // Modal Réactiver
        function openReactivateModal(userId, userName) {
            document.getElementById('reactivateUserId').value = userId;
            document.getElementById('reactivateUserName').textContent = userName;
            document.getElementById('reactivateModal').classList.remove('hidden');
        }
        function closeReactivateModal() {
            document.getElementById('reactivateModal').classList.add('hidden');
        }

        // Modal Supprimer
        function openDeleteModal(userId, userName) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Fermer les modals en cliquant en dehors
        window.onclick = function(event) {
            if (event.target.id === 'suspendModal') closeSuspendModal();
            if (event.target.id === 'banModal') closeBanModal();
            if (event.target.id === 'reactivateModal') closeReactivateModal();
            if (event.target.id === 'deleteModal') closeDeleteModal();
        }
    </script>
</body>
</html>
