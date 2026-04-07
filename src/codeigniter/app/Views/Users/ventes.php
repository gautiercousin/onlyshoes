<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => ($title ?? 'Mes ventes') . ' - OnlyShoes']) ?>
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
                    <h1 class="text-2xl font-bold text-gray-900">Mes ventes</h1>
                    <p class="text-gray-500 text-sm">
                        Suivez les ventes et expéditions de vos produits.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Historique des ventes</h2>
                <span class="text-xs text-gray-400">Fonctionnalite a venir</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="border border-gray-200 rounded-2xl p-5">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Ce mois-ci</p>
                    <p class="text-2xl font-semibold text-green-600"><?= number_format($stats['montant_mois'] ?? 0, 2, ',', ' ') ?> €</p>
                    <p class="text-xs text-gray-500 mt-1">Benefices mensuels</p>
                </div>
                <div class="border border-gray-200 rounded-2xl p-5">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Cette annee</p>
                    <p class="text-2xl font-semibold text-green-600"><?= number_format($stats['montant_annee'] ?? 0, 2, ',', ' ') ?> €</p>
                    <p class="text-xs text-gray-500 mt-1">Benefices annuels</p>
                </div>
                <div class="border border-gray-200 rounded-2xl p-5">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Depuis toujours</p>
                    <p class="text-2xl font-semibold text-green-600"><?= number_format($stats['montant_total'] ?? 0, 2, ',', ' ') ?> €</p>
                    <p class="text-xs text-gray-500 mt-1">Benefices totaux</p>
                </div>
            </div>

            <?php if (empty($ventes)): ?>
                <div class="border border-dashed border-gray-200 rounded-2xl p-6 text-center text-gray-500">
                    Vous n'avez pas encore vendu de produit.
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($ventes as $vente): ?>
                        <div class="border border-gray-200 rounded-2xl p-6 hover:border-green-500 transition">
                            <div class="flex items-start gap-4">
                                <!-- Image du produit -->
                                <?php if (!empty($vente['image_url'])): ?>
                                    <img src="<?= esc($vente['image_url']) ?>"
                                         alt="<?= esc($vente['annonce_titre']) ?>"
                                         class="w-24 h-24 object-cover rounded-xl"
                                         onerror="this.src='/notfound.webp'">
                                <?php else: ?>
                                    <div class="w-24 h-24 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">Pas d'image</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Détails de la vente -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <a href="/produit/<?= esc($vente['id_annonce']) ?>" class="text-lg font-semibold text-gray-900 hover:text-green-600 transition">
                                                <?= esc($vente['annonce_titre']) ?>
                                            </a>
                                            <p class="text-sm text-gray-500">
                                                Vendu le <?= date('d/m/Y à H:i', strtotime($vente['date'])) ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Acheteur: <a href="/utilisateur/profil/<?= esc($vente['acheteur_id']) ?>" class="font-medium text-green-600 hover:underline">
                                                    <?= esc($vente['acheteur_prenom']) ?> <?= esc($vente['acheteur_nom']) ?>
                                                </a>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-600"><?= number_format($vente['montant_paye'], 2, ',', ' ') ?>€</p>
                                        </div>
                                    </div>

                                    <!-- Statuts -->
                                    <div class="flex items-center gap-4 mt-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-gray-500">Commande:</span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                <?php
                                                    switch($vente['statut_commande']) {
                                                        case 'en_preparation': echo 'bg-blue-100 text-blue-700'; break;
                                                        case 'expediee': echo 'bg-purple-100 text-purple-700'; break;
                                                        case 'livree': echo 'bg-green-100 text-green-700'; break;
                                                        case 'annulee': echo 'bg-red-100 text-red-700'; break;
                                                        default: echo 'bg-gray-100 text-gray-700';
                                                    }
                                                ?>">
                                                <?= esc(ucfirst(str_replace('_', ' ', $vente['statut_commande']))) ?>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-gray-500">Paiement:</span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                <?php
                                                    switch($vente['statut_paiement']) {
                                                        case 'valide': echo 'bg-green-100 text-green-700'; break;
                                                        case 'en_attente': echo 'bg-yellow-100 text-yellow-700'; break;
                                                        case 'refuse': echo 'bg-red-100 text-red-700'; break;
                                                        default: echo 'bg-gray-100 text-gray-700';
                                                    }
                                                ?>">
                                                <?= esc(ucfirst(str_replace('_', ' ', $vente['statut_paiement']))) ?>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-gray-500">Méthode:</span>
                                            <span class="text-xs text-gray-600">
                                                <?= esc(ucfirst(str_replace('_', ' ', $vente['type_paiement']))) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Actions de gestion de la commande -->
                                    <?php if ($vente['statut_commande'] !== 'livree' && $vente['statut_commande'] !== 'annulee'): ?>
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="flex items-center gap-3">
                                                <span class="text-xs font-medium text-gray-500">Mettre à jour:</span>
                                                <?php if ($vente['statut_commande'] === 'en_preparation'): ?>
                                                    <form action="/ventes/update-statut" method="POST" class="inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id_commande" value="<?= esc($vente['id_commande']) ?>">
                                                        <input type="hidden" name="statut" value="expediee">
                                                        <button type="submit" class="px-3 py-1 text-xs font-medium bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                                            Marquer comme expédiée
                                                        </button>
                                                    </form>
                                                <?php elseif ($vente['statut_commande'] === 'expediee'): ?>
                                                    <form action="/ventes/update-statut" method="POST" class="inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id_commande" value="<?= esc($vente['id_commande']) ?>">
                                                        <input type="hidden" name="statut" value="livree">
                                                        <button type="submit" class="px-3 py-1 text-xs font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                                            Marquer comme livrée
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
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
