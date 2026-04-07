<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Gestion des Utilisateurs - Admin OnlyShoes']) ?>
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
        $current_page = 'utilisateurs';
        echo view('components/admin_sidebar', ['current_page' => $current_page]); 
        ?>

        <!-- Main Content -->
        <main class="w-full lg:ml-64 flex-1 p-4 sm:p-6 lg:p-8 pt-20 lg:pt-8 overflow-x-hidden max-w-full">
            <?php 
            echo view('components/admin_header', [
                'page_title' => 'Gestion des Utilisateurs',
                'page_subtitle' => 'Rechercher et consulter les comptes utilisateurs',
                'admin_nom' => $admin_nom ?? 'Administrateur'
            ]); 
            ?>

            <!-- Filtres et Recherche -->
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
                <form method="GET" action="/admin/utilisateurs" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Recherche -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="<?= esc($search ?? '') ?>"
                            placeholder="Nom, prénom ou email..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    
                    <!-- Filtre Type de compte -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de compte</label>
                        <select name="type_compte" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                            <option value="tous" <?= ($type_compte_actuel ?? 'tous') === 'tous' ? 'selected' : '' ?>>Tous</option>
                            <option value="standard" <?= ($type_compte_actuel ?? '') === 'standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="admin" <?= ($type_compte_actuel ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <!-- Filtre Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                            <option value="tous" <?= ($status_actuel ?? 'tous') === 'tous' ? 'selected' : '' ?>>Tous</option>
                            <option value="actif" <?= ($status_actuel ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
                            <option value="suspendu" <?= ($status_actuel ?? '') === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                            <option value="bannis" <?= ($status_actuel ?? '') === 'bannis' ? 'selected' : '' ?>>Bannis</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">
                            Appliquer les filtres
                        </button>
                        <a href="/admin/utilisateurs" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition text-center">
                            Réinitialiser
                        </a>
                    </div>
                </form>
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

            <!-- Liste des utilisateurs -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Utilisateur</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Email</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Inscription</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($utilisateurs)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Aucun utilisateur trouvé
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($utilisateurs as $user): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100">
                                                    <img src="https://api.dicebear.com/9.x/thumbs/svg?seed=<?= urlencode($user['id_utilisateur'] ?? ($user['email'] ?? 'default')) ?>" alt="Avatar" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900"><?= esc($user['prenom']) ?> <?= esc($user['nom']) ?></p>
                                                    <p class="text-xs text-gray-500 lg:hidden"><?= esc($user['email']) ?></p>
                                                    <p class="text-xs text-gray-500">ID: <?= substr($user['id_utilisateur'], 0, 8) ?>...</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-700 hidden lg:table-cell">
                                            <?= esc($user['email']) ?>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <?php if ($user['type_compte'] === 'admin'): ?>
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Admin
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Standard
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <?php if ($user['status'] === 'actif'): ?>
                                                <span class="inline-flex px-2 sm:px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Actif
                                                </span>
                                            <?php elseif ($user['status'] === 'suspendu'): ?>
                                                <span class="inline-flex px-2 sm:px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    ⏸ Suspendu
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex px-2 sm:px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    ✖ Bannis
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                                            <?= date('d/m/Y', strtotime($user['date_creation'])) ?>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <!-- Bouton Voir Détails -->
                                                <?php if ($user['type_compte'] !== 'admin'): ?>
                                                <a href="/admin/utilisateur/<?= $user['id_utilisateur'] ?>" 
                                                   class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <span class="hidden sm:inline">Voir détails</span>
                                                </a>
                                                <?php else: ?>
                                                <button 
                                                   class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed flex items-center space-x-1" disabled>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <span class="hidden sm:inline">Protégé</span>
                                                </button>
                                                <?php endif; ?>
                                            </div>
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
                    'base_url' => base_url('admin/utilisateurs')
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
