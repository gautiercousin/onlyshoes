<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Détails de l\'annonce - Administration']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
</style>
<body class="bg-gray-50 min-h-screen overflow-x-hidden">
    <div class="flex overflow-x-hidden max-w-full">
        <?php 
        $current_page = 'signalements';
        echo view('components/admin_sidebar', ['current_page' => $current_page]); 
        ?>

        <!-- Main Content -->
        <main class="w-full lg:ml-64 flex-1 p-4 sm:p-6 lg:p-8 pt-20 lg:pt-8 overflow-x-hidden max-w-full">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="<?= base_url('admin/signalements?type=annonce') ?>" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour aux signalements
                </a>
            </div>

            <?php 
            echo view('components/admin_header', [
                'page_title' => 'Détails de l\'annonce signalée',
                'page_subtitle' => 'Examinez les détails et les signalements de cette annonce',
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Détails de l'annonce -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informations principales -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4"><?= esc($annonce['titre']) ?></h2>
                        
                        <!-- Image -->
                        <?php if (!empty($annonce['image_url'])): ?>
                            <img src="<?= esc($annonce['image_url']) ?>" 
                                 alt="<?= esc($annonce['titre']) ?>" 
                                 class="w-full h-96 object-cover rounded-xl mb-6">
                        <?php else: ?>
                            <div class="w-full h-96 bg-gray-100 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <!-- Description -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-600"><?= nl2br(esc($annonce['description'] ?? 'Aucune description')) ?></p>
                        </div>

                        <!-- Caractéristiques -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-500 mb-1">Prix</p>
                                <p class="text-2xl font-bold text-gray-900"><?= number_format($annonce['prix'], 2) ?> €</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-500 mb-1">État</p>
                                <p class="text-lg font-semibold text-gray-900 capitalize"><?= esc($annonce['etat'] ?? 'N/A') ?></p>
                            </div>
                            <?php if (!empty($annonce['marque_nom'])): ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-500 mb-1">Marque</p>
                                <p class="text-lg font-semibold text-gray-900"><?= esc($annonce['marque_nom']) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($annonce['couleur_nom'])): ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-500 mb-1">Couleur</p>
                                <p class="text-lg font-semibold text-gray-900"><?= esc($annonce['couleur_nom']) ?></p>
                            </div>
                            <?php endif; ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-500 mb-1">Taille</p>
                                <p class="text-lg font-semibold text-gray-900"><?= esc($annonce['taille']) ?> (<?= esc($annonce['taille_systeme']) ?>)</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-500 mb-1">Publication</p>
                                <p class="text-lg font-semibold text-gray-900"><?= date('d/m/Y', strtotime($annonce['date_publication'])) ?></p>
                            </div>
                        </div>

                        <!-- Informations du vendeur -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Vendeur</h3>
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900"><?= esc($annonce['vendeur_prenom'] . ' ' . $annonce['vendeur_nom']) ?></p>
                                    <p class="text-sm text-gray-500"><?= esc($annonce['vendeur_email']) ?></p>
                                </div>
                                <a href="<?= base_url('utilisateur/profil/' . $annonce['id_vendeur']) ?>" 
                                   class="ml-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition text-sm font-medium">
                                    Voir le profil
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Actions d'administration -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            <button onclick="confirmerSuppressionAnnonce('<?= $annonce['id_annonce'] ?>', '<?= esc($annonce['titre']) ?>')"
                                    class="w-full flex items-center justify-center space-x-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Supprimer l'annonce</span>
                            </button>
                            <button onclick="confirmerRejetSignalements('<?= $annonce['id_annonce'] ?>', '<?= esc($annonce['titre']) ?>')"
                                    class="w-full flex items-center justify-center space-x-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Rejeter les signalements (conserver l'annonce)</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Liste des signalements -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Signalements (<?= count($annonce['signalements'] ?? []) ?>)
                        </h3>
                        
                        <?php if (!empty($annonce['signalements'])): ?>
                            <div class="space-y-4 max-h-[600px] overflow-y-auto">
                                <?php foreach ($annonce['signalements'] as $index => $signalement): ?>
                                    <div class="border border-gray-200 rounded-xl p-4 <?= $signalement['statut'] === 'en_attente' ? 'bg-orange-50 border-orange-200' : 'bg-gray-50' ?>">
                                        <div class="flex items-start justify-between mb-2">
                                            <span class="text-xs font-medium text-gray-500">#<?= $index + 1 ?></span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                                <?= $signalement['statut'] === 'en_attente' ? 'bg-orange-100 text-orange-800' : 
                                                   ($signalement['statut'] === 'traite' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') ?>">
                                                <?= esc($signalement['statut']) ?>
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="text-sm font-semibold text-gray-900 mb-1">Motif</p>
                                            <p class="text-sm text-gray-700"><?= esc($signalement['motif']) ?></p>
                                        </div>
                                        
                                        <?php if (!empty($signalement['description'])): ?>
                                            <div class="mb-3">
                                                <p class="text-sm font-semibold text-gray-900 mb-1">Description</p>
                                                <p class="text-sm text-gray-600"><?= nl2br(esc($signalement['description'])) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="border-t border-gray-200 pt-3 mt-3">
                                            <p class="text-xs text-gray-500 mb-2">Signalé par</p>
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= esc($signalement['auteur_prenom'] . ' ' . $signalement['auteur_nom']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500"><?= esc($signalement['auteur_email']) ?></p>
                                            <p class="text-xs text-gray-400 mt-2">
                                                <?= date('d/m/Y à H:i', strtotime($signalement['date'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <p class="text-gray-500">Aucun signalement</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals (mêmes que dans annonces_signalees.php) -->
    <div id="modalSuppressionAnnonce" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 text-center mb-4">
                Voulez-vous vraiment supprimer l'annonce "<span id="titreSuppression" class="font-semibold"></span>" ?
            </p>
            <p class="text-sm text-gray-500 text-center mb-6">Cette action est irréversible.</p>
            
            <form id="formSuppressionAnnonce" method="POST" action="<?= base_url('admin/supprimer-annonce') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id_annonce" id="idAnnonceSuppression">
                
                <div class="mb-4">
                    <label for="raisonSuppression" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison de la suppression (optionnel)
                    </label>
                    <textarea name="raison" id="raisonSuppression" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Ex: Contenu inapproprié..."></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="fermerModalSuppression()"
                            class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition font-medium">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition font-medium">
                        Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalRejetSignalements" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Rejeter les signalements</h3>
            <p class="text-gray-600 text-center mb-4">
                Rejeter les signalements de l'annonce "<span id="titreRejet" class="font-semibold"></span>" ?
            </p>
            <p class="text-sm text-gray-500 text-center mb-6">L'annonce sera conservée.</p>
            
            <form id="formRejetSignalements" method="POST" action="<?= base_url('admin/rejeter-signalements-annonce') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id_annonce" id="idAnnonceRejet">
                
                <div class="mb-4">
                    <label for="raisonRejet" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison du rejet (optionnel)
                    </label>
                    <textarea name="raison" id="raisonRejet" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Ex: Signalements non fondés..."></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="fermerModalRejet()"
                            class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition font-medium">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition font-medium">
                        Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmerSuppressionAnnonce(idAnnonce, titre) {
            document.getElementById('idAnnonceSuppression').value = idAnnonce;
            document.getElementById('titreSuppression').textContent = titre;
            document.getElementById('modalSuppressionAnnonce').classList.remove('hidden');
        }

        function fermerModalSuppression() {
            document.getElementById('modalSuppressionAnnonce').classList.add('hidden');
        }

        function confirmerRejetSignalements(idAnnonce, titre) {
            document.getElementById('idAnnonceRejet').value = idAnnonce;
            document.getElementById('titreRejet').textContent = titre;
            document.getElementById('modalRejetSignalements').classList.remove('hidden');
        }

        function fermerModalRejet() {
            document.getElementById('modalRejetSignalements').classList.add('hidden');
        }

        document.getElementById('modalSuppressionAnnonce').addEventListener('click', function(e) {
            if (e.target === this) fermerModalSuppression();
        });

        document.getElementById('modalRejetSignalements').addEventListener('click', function(e) {
            if (e.target === this) fermerModalRejet();
        });
    </script>
</body>
</html>
