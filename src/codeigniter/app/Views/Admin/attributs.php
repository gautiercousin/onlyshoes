<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Gestion des Attributs - Admin OnlyShoes']) ?>
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
        $current_page = 'attributs';
        echo view('components/admin_sidebar', ['current_page' => $current_page, 'signalements_en_attente' => 0]); 
        ?>

        <!-- Main Content -->
        <main class="w-full lg:ml-64 flex-1 p-4 sm:p-6 lg:p-8 pt-20 lg:pt-8 overflow-x-hidden max-w-full">
            <?php 
            echo view('components/admin_header', [
                'page_title' => 'Gestion des Attributs',
                'page_subtitle' => 'Gérer les marques, couleurs, matériaux et tailles',
                'admin_nom' => $admin_nom ?? 'Administrateur'
            ]); 
            ?>

            <!-- Alerts -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Tabs Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto">
                        <button onclick="showTab('marques')" id="tab-marques" class="tab-btn border-b-2 border-green-600 text-green-600 py-4 px-1 font-semibold whitespace-nowrap">
                            Marques
                        </button>
                        <button onclick="showTab('couleurs')" id="tab-couleurs" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-semibold whitespace-nowrap">
                            Couleurs
                        </button>
                        <button onclick="showTab('materiaux')" id="tab-materiaux" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-semibold whitespace-nowrap">
                            Matériaux
                        </button>
                        <button onclick="showTab('tailles')" id="tab-tailles" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-semibold whitespace-nowrap">
                            Systèmes de Tailles
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Marques Tab -->
            <div id="content-marques" class="tab-content">
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ajouter une Marque</h3>
                    <form action="<?= base_url('admin/ajouterMarque') ?>" method="post" class="flex gap-4">
                        <input type="text" name="nom" required placeholder="Nom de la marque (ex: Nike, Adidas...)" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                            Ajouter
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Liste des Marques (<?= count($marques) ?>)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($marques)): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                            Aucune marque enregistrée
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($marques as $marque): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= esc($marque['id_marque']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= esc($marque['nom']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <form action="<?= base_url('admin/supprimerMarque') ?>" method="post" class="inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette marque ?');">
                                                    <input type="hidden" name="id_marque" value="<?= esc($marque['id_marque']) ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Couleurs Tab -->
            <div id="content-couleurs" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ajouter une Couleur</h3>
                    <form action="<?= base_url('admin/ajouterCouleur') ?>" method="post" class="flex gap-4">
                        <input type="text" name="nom" required placeholder="Nom de la couleur (ex: Rouge, Bleu...)" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                            Ajouter
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Liste des Couleurs (<?= count($couleurs) ?>)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($couleurs)): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                            Aucune couleur enregistrée
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($couleurs as $couleur): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= esc($couleur['id_couleur']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= esc($couleur['nom']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <form action="<?= base_url('admin/supprimerCouleur') ?>" method="post" class="inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette couleur ?');">
                                                    <input type="hidden" name="id_couleur" value="<?= esc($couleur['id_couleur']) ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Matériaux Tab -->
            <div id="content-materiaux" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ajouter un Matériau</h3>
                    <form action="<?= base_url('admin/ajouterMateriau') ?>" method="post" class="flex gap-4">
                        <input type="text" name="nom" required placeholder="Nom du matériau (ex: Cuir, Textile...)" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                            Ajouter
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Liste des Matériaux (<?= count($materiaux) ?>)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($materiaux)): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                            Aucun matériau enregistré
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($materiaux as $materiau): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= esc($materiau['id_materiau']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= esc($materiau['nom']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <form action="<?= base_url('admin/supprimerMateriau') ?>" method="post" class="inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce matériau ?');">
                                                    <input type="hidden" name="id_materiau" value="<?= esc($materiau['id_materiau']) ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tailles Tab -->
            <div id="content-tailles" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Systèmes de Tailles Disponibles</h3>
                    <p class="text-gray-600 mb-6">
                        Les utilisateurs peuvent choisir parmi ces systèmes de tailles lors de la création d'une annonce.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php foreach ($systemes_taille as $systeme): ?>
                            <div class="border border-gray-200 rounded-lg p-6 text-center">
                                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-bold text-gray-900 mb-2"><?= esc($systeme) ?></h4>
                                <p class="text-sm text-gray-500">
                                    <?php 
                                    switch($systeme) {
                                        case 'EU': echo 'Système Européen'; break;
                                        case 'US': echo 'Système Américain'; break;
                                        case 'UK': echo 'Système Britannique'; break;
                                    }
                                    ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-blue-800">
                            <strong>Note :</strong> Les systèmes de tailles sont définis dans la base de données et utilisés lors de la création d'annonces. 
                            Les valeurs autorisées sont : EU (Europe), US (États-Unis), et UK (Royaume-Uni).
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Tab switching functionality
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-green-600', 'text-green-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById(`content-${tabName}`).classList.remove('hidden');

            // Add active state to selected tab
            const activeTab = document.getElementById(`tab-${tabName}`);
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-green-600', 'text-green-600');
        }

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
