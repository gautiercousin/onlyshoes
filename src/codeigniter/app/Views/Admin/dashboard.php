<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Dashboard Admin - OnlyShoes']) ?>
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
        $current_page = 'dashboard';
        echo view('components/admin_sidebar', ['current_page' => $current_page, 'signalements_en_attente' => $signalements_en_attente ?? 0]); 
        ?>

        <!-- Main Content -->
        <main class="w-full lg:ml-64 flex-1 p-4 sm:p-6 lg:p-8 pt-20 lg:pt-8 overflow-x-hidden max-w-full">
            <?php 
            echo view('components/admin_header', [
                'page_title' => 'Dashboard',
                'page_subtitle' => 'Vue d\'ensemble de votre plateforme',
                'admin_nom' => $admin_nom ?? 'Administrateur'
            ]); 
            ?>

            <!-- Alert Success -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- Stats Grid - Centrées -->
            <div class="max-w-5xl mx-auto mb-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <!-- Stat Card 1 -->
                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide">Total Comptes</p>
                    <p class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2"><?= number_format($total_comptes ?? 0) ?></p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2">Utilisateurs enregistrés</p>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide">Total Produits</p>
                    <p class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2"><?= number_format($total_produits ?? 0) ?></p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2">Annonces publiées</p>
                </div>

                <!-- Stat Card 3 - Signalements -->
                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 hover:shadow-md transition cursor-pointer" onclick="window.location.href='/admin/signalements'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide">Signalements en attente</p>
                    <p class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2"><?= number_format($signalements_en_attente ?? 0) ?></p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2">Tous types confondus</p>
                </div>
            </div>
        </div>

            <!-- Additional Content -->
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 pb-4 border-b border-gray-100">Activité récente</h2>
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500">Cette section sera bientôt disponible</p>
                </div>
            </div>
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
