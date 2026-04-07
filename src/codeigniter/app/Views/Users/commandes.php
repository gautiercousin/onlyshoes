<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => ($title ?? 'Mes commandes') . ' - OnlyShoes']) ?>
<body class="bg-gray-50 overflow-x-hidden">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
        }
    </style>

    <?= view('components/header') ?>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
            <div class="flex items-center gap-6">
                <?= view('components/user-avatar', [
                    'prenom' => $user['prenom'] ?? '',
                    'nom' => $user['nom'] ?? '',
                    'size' => 'w-16 h-16',
                    'textSize' => 'text-xl',
                    'isAdmin' => ($user['type_compte'] ?? '') === 'admin'
                ]) ?>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Mes commandes</h1>
                    <p class="text-gray-500 text-sm">
                        Consultez l'historique de vos commandes et leur statut.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Historique</h2>
            </div>

            <?php if (empty($commandes)): ?>
                <div class="border border-dashed border-gray-200 rounded-2xl p-6 text-center text-gray-500">
                    Vous n'avez pas encore passé de commande.
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($commandes as $commande): ?>
                        <div class="border border-gray-200 rounded-2xl p-6 hover:border-green-500 transition">
                            <div class="flex items-start gap-4">
                                <!-- Image du produit -->
                                <?php if (!empty($commande['image_url'])): ?>
                                    <img src="<?= esc($commande['image_url']) ?>"
                                         alt="<?= esc($commande['annonce_titre']) ?>"
                                         class="w-24 h-24 object-cover rounded-xl"
                                         onerror="this.src='/notfound.webp'">
                                <?php else: ?>
                                    <div class="w-24 h-24 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">Pas d'image</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Détails de la commande -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <a href="/produit/<?= esc($commande['id_annonce']) ?>" class="text-lg font-semibold text-gray-900 hover:text-green-600 transition">
                                                <?= esc($commande['annonce_titre']) ?>
                                            </a>
                                            <p class="text-sm text-gray-500">
                                                Commandé le <?= date('d/m/Y à H:i', strtotime($commande['date'])) ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Vendeur: <a href="/utilisateur/profil/<?= esc($commande['vendeur_id']) ?>" class="font-medium text-green-600 hover:underline">
                                                    <?= esc($commande['vendeur_prenom']) ?> <?= esc($commande['vendeur_nom']) ?>
                                                </a>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-gray-900"><?= number_format($commande['montant_paye'], 2, ',', ' ') ?>€</p>
                                        </div>
                                    </div>

                                    <!-- Statuts -->
                                    <div class="flex items-center gap-4 mt-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-gray-500">Commande:</span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                <?php
                                                    switch($commande['statut']) {
                                                        case 'en_preparation': echo 'bg-blue-100 text-blue-700'; break;
                                                        case 'expediee': echo 'bg-purple-100 text-purple-700'; break;
                                                        case 'livree': echo 'bg-green-100 text-green-700'; break;
                                                        case 'annulee': echo 'bg-red-100 text-red-700'; break;
                                                        default: echo 'bg-gray-100 text-gray-700';
                                                    }
                                                ?>">
                                                <?= esc(ucfirst(str_replace('_', ' ', $commande['statut']))) ?>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-gray-500">Paiement:</span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                <?php
                                                    switch($commande['statut_paiement']) {
                                                        case 'valide': echo 'bg-green-100 text-green-700'; break;
                                                        case 'en_attente': echo 'bg-yellow-100 text-yellow-700'; break;
                                                        case 'refuse': echo 'bg-red-100 text-red-700'; break;
                                                        default: echo 'bg-gray-100 text-gray-700';
                                                    }
                                                ?>">
                                                <?= esc(ucfirst(str_replace('_', ' ', $commande['statut_paiement']))) ?>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-gray-500">Méthode:</span>
                                            <span class="text-xs text-gray-600">
                                                <?= esc(ucfirst(str_replace('_', ' ', $commande['type_paiement']))) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Actions - Review button -->
                                    <?php if ($commande['statut'] === 'livree' && $commande['statut_paiement'] === 'valide'): ?>
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <a href="<?= base_url('utilisateur/profil/' . $commande['vendeur_id']) ?>#reviews"
                                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <?= !empty($commande['id_review']) ? 'Voir mon avis' : 'Laisser un avis' ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?= view('components/pagination', ['pagination' => $pagination]) ?>
        </div>
    </main>

    <?= view('components/footer') ?>
</body>
</html>
