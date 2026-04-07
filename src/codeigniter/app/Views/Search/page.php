<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Recherche' . ($query ? ' : ' . $query : '') . ' - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .glass-effect {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
    }
    .shoe-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .shoe-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
    }
</style>
<body class="bg-gray-50 min-h-screen">
    <?= view('components/header') ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <!-- le header de la recherche -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                <?php if ($query): ?>
                    Résultats pour "<?= esc($query) ?>"
                <?php else: ?>
                    Rechercher des sneakers
                <?php endif; ?>
            </h1>
            
            <!-- le formulaire de recherche -->
            <form action="/recherche" method="GET" class="flex gap-4 max-w-2xl">
                <!-- Préserver les filtres actifs -->
                <?php if (!empty($marque_selectionnee)): ?>
                    <input type="hidden" name="marque" value="<?= esc($marque_selectionnee) ?>">
                <?php endif; ?>
                <?php if (!empty($couleur_selectionnee)): ?>
                    <input type="hidden" name="couleur" value="<?= esc($couleur_selectionnee) ?>">
                <?php endif; ?>
                <?php if (!empty($materiau_selectionne)): ?>
                    <input type="hidden" name="materiau" value="<?= esc($materiau_selectionne) ?>">
                <?php endif; ?>
                <?php if (!empty($etat_selectionne)): ?>
                    <input type="hidden" name="etat" value="<?= esc($etat_selectionne) ?>">
                <?php endif; ?>
                <?php if (!empty($taille_systeme_selectionne)): ?>
                    <input type="hidden" name="taille_systeme" value="<?= esc($taille_systeme_selectionne) ?>">
                <?php endif; ?>
                <?php if (!empty($taille_selectionnee)): ?>
                    <input type="hidden" name="taille" value="<?= esc($taille_selectionnee) ?>">
                <?php endif; ?>
                <?php if (!empty($prix_min)): ?>
                    <input type="hidden" name="prix_min" value="<?= esc($prix_min) ?>">
                <?php endif; ?>
                <?php if (!empty($prix_max)): ?>
                    <input type="hidden" name="prix_max" value="<?= esc($prix_max) ?>">
                <?php endif; ?>

                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="q" value="<?= esc($query) ?>"
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                        placeholder="Rechercher une marque, un modèle...">
                </div>
                <button type="submit" class="bg-green-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-green-700 transition">
                    Rechercher
                </button>
            </form>
        </div>

        <!-- Filtres actifs -->
        <?php
        $hasActiveFilters = !empty($marque_selectionnee) || !empty($couleur_selectionnee) || !empty($materiau_selectionne) || !empty($etat_selectionne) || !empty($taille_systeme_selectionne) || !empty($taille_selectionnee) || !empty($prix_min) || !empty($prix_max);
        ?>
        <?php if ($hasActiveFilters): ?>
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="text-sm text-gray-600 font-medium">Filtres actifs:</span>

            <?php if (!empty($marque_selectionnee)):
                $marque_nom = array_values(array_filter($marques, fn($m) => $m['id_marque'] == $marque_selectionnee))[0]['nom'] ?? '';
            ?>
                <a href="/recherche?q=<?= urlencode($query) ?>&couleur=<?= urlencode($couleur_selectionnee) ?>&materiau=<?= urlencode($materiau_selectionne) ?>&etat=<?= urlencode($etat_selectionne) ?>&taille_systeme=<?= urlencode($taille_systeme_selectionne) ?>&taille=<?= urlencode($taille_selectionnee) ?>&prix_min=<?= urlencode($prix_min) ?>&prix_max=<?= urlencode($prix_max) ?>"
                   class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium hover:bg-green-200 transition">
                    <?= esc($marque_nom) ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if (!empty($couleur_selectionnee)):
                $couleur_nom = array_values(array_filter($couleurs, fn($c) => $c['id_couleur'] == $couleur_selectionnee))[0]['nom'] ?? '';
            ?>
                <a href="/recherche?q=<?= urlencode($query) ?>&marque=<?= urlencode($marque_selectionnee) ?>&materiau=<?= urlencode($materiau_selectionne) ?>&etat=<?= urlencode($etat_selectionne) ?>&taille_systeme=<?= urlencode($taille_systeme_selectionne) ?>&taille=<?= urlencode($taille_selectionnee) ?>&prix_min=<?= urlencode($prix_min) ?>&prix_max=<?= urlencode($prix_max) ?>"
                   class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium hover:bg-green-200 transition">
                    <?= esc($couleur_nom) ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if (!empty($materiau_selectionne)):
                $materiau_nom = array_values(array_filter($materiaux, fn($m) => $m['id_materiau'] == $materiau_selectionne))[0]['nom'] ?? '';
            ?>
                <a href="/recherche?q=<?= urlencode($query) ?>&marque=<?= urlencode($marque_selectionnee) ?>&couleur=<?= urlencode($couleur_selectionnee) ?>&etat=<?= urlencode($etat_selectionne) ?>&taille_systeme=<?= urlencode($taille_systeme_selectionne) ?>&taille=<?= urlencode($taille_selectionnee) ?>&prix_min=<?= urlencode($prix_min) ?>&prix_max=<?= urlencode($prix_max) ?>"
                   class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium hover:bg-green-200 transition">
                    <?= esc($materiau_nom) ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if (!empty($etat_selectionne)):
                $etat_label = array_values(array_filter($etats, fn($e) => $e['value'] == $etat_selectionne))[0]['label'] ?? '';
            ?>
                <a href="/recherche?q=<?= urlencode($query) ?>&marque=<?= urlencode($marque_selectionnee) ?>&couleur=<?= urlencode($couleur_selectionnee) ?>&materiau=<?= urlencode($materiau_selectionne) ?>&taille_systeme=<?= urlencode($taille_systeme_selectionne) ?>&taille=<?= urlencode($taille_selectionnee) ?>&prix_min=<?= urlencode($prix_min) ?>&prix_max=<?= urlencode($prix_max) ?>"
                   class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium hover:bg-green-200 transition">
                    <?= esc($etat_label) ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if (!empty($taille_systeme_selectionne) || !empty($taille_selectionnee)): ?>
                <a href="/recherche?q=<?= urlencode($query) ?>&marque=<?= urlencode($marque_selectionnee) ?>&couleur=<?= urlencode($couleur_selectionnee) ?>&materiau=<?= urlencode($materiau_selectionne) ?>&etat=<?= urlencode($etat_selectionne) ?>&prix_min=<?= urlencode($prix_min) ?>&prix_max=<?= urlencode($prix_max) ?>"
                   class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium hover:bg-green-200 transition">
                    <?= !empty($taille_selectionnee) ? esc($taille_selectionnee) : '' ?><?= !empty($taille_systeme_selectionne) ? ' (' . esc($taille_systeme_selectionne) . ')' : '' ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if (!empty($prix_min) || !empty($prix_max)): ?>
                <a href="/recherche?q=<?= urlencode($query) ?>&marque=<?= urlencode($marque_selectionnee) ?>&couleur=<?= urlencode($couleur_selectionnee) ?>&materiau=<?= urlencode($materiau_selectionne) ?>&etat=<?= urlencode($etat_selectionne) ?>&taille_systeme=<?= urlencode($taille_systeme_selectionne) ?>&taille=<?= urlencode($taille_selectionnee) ?>"
                   class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium hover:bg-green-200 transition">
                    <?php if (!empty($prix_min) && !empty($prix_max)): ?>
                        <?= esc($prix_min) ?>€ - <?= esc($prix_max) ?>€
                    <?php elseif (!empty($prix_min)): ?>
                        > <?= esc($prix_min) ?>€
                    <?php else: ?>
                        < <?= esc($prix_max) ?>€
                    <?php endif; ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <a href="/recherche<?= !empty($query) ? '?q=' . urlencode($query) : '' ?>"
               class="inline-flex items-center gap-1 px-3 py-1 text-gray-600 hover:text-gray-900 text-sm font-medium transition">
                Tout effacer
            </a>
        </div>
        <?php endif; ?>

        <!-- Filtres de recherche (collapsible) -->
        <div class="bg-white rounded-2xl shadow-sm mb-8">
            <button type="button" onclick="document.getElementById('filterPanel').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition rounded-2xl">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Filtres</h3>
                    <?php if ($hasActiveFilters): ?>
                        <span class="px-2 py-0.5 bg-green-600 text-white text-xs rounded-full font-medium">
                            <?= count(array_filter([$marque_selectionnee, $couleur_selectionnee, $materiau_selectionne, $etat_selectionne, ($taille_systeme_selectionne || $taille_selectionnee), ($prix_min || $prix_max)])) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" id="filterIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="filterPanel" class="<?= $hasActiveFilters ? '' : 'hidden' ?> border-t border-gray-200">
                <form action="/recherche" method="GET" id="filterForm" class="p-4">
                    <!-- Conserver la requête de recherche -->
                    <?php if (!empty($query)): ?>
                        <input type="hidden" name="q" value="<?= esc($query) ?>">
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Filtre Marque -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Marque</label>
                            <select name="marque" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Toutes</option>
                                <?php foreach ($marques as $marque): ?>
                                    <option value="<?= esc($marque['id_marque']) ?>" <?= $marque_selectionnee == $marque['id_marque'] ? 'selected' : '' ?>>
                                        <?= esc($marque['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtre Couleur -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Couleur</label>
                            <select name="couleur" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Toutes</option>
                                <?php foreach ($couleurs as $couleur): ?>
                                    <option value="<?= esc($couleur['id_couleur']) ?>" <?= $couleur_selectionnee == $couleur['id_couleur'] ? 'selected' : '' ?>>
                                        <?= esc($couleur['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtre Matériau -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Matériau</label>
                            <select name="materiau" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Tous</option>
                                <?php foreach ($materiaux as $materiau): ?>
                                    <option value="<?= esc($materiau['id_materiau']) ?>" <?= $materiau_selectionne == $materiau['id_materiau'] ? 'selected' : '' ?>>
                                        <?= esc($materiau['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtre État -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">État</label>
                            <select name="etat" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Tous</option>
                                <?php foreach ($etats as $etat_option): ?>
                                    <option value="<?= esc($etat_option['value']) ?>" <?= $etat_selectionne == $etat_option['value'] ? 'selected' : '' ?>>
                                        <?= esc($etat_option['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtre Système de taille -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Système</label>
                            <select name="taille_systeme" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Tous</option>
                                <?php foreach ($taille_systemes as $systeme): ?>
                                    <option value="<?= esc($systeme['value']) ?>" <?= $taille_systeme_selectionne == $systeme['value'] ? 'selected' : '' ?>>
                                        <?= esc($systeme['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtre Taille -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Taille</label>
                            <input type="text" name="taille" value="<?= esc($taille_selectionnee) ?>"
                                   placeholder="Ex: 42, 10.5"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                                   onkeypress="if(event.key === 'Enter') { document.getElementById('filterForm').submit(); }">
                        </div>

                        <!-- Filtre Prix Min -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Prix min (€)</label>
                            <input type="number" name="prix_min" value="<?= esc($prix_min) ?>"
                                   placeholder="0"
                                   min="0"
                                   step="0.01"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                                   onkeypress="if(event.key === 'Enter') { document.getElementById('filterForm').submit(); }">
                        </div>

                        <!-- Filtre Prix Max -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Prix max (€)</label>
                            <input type="number" name="prix_max" value="<?= esc($prix_max) ?>"
                                   placeholder="1000"
                                   min="0"
                                   step="0.01"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                                   onkeypress="if(event.key === 'Enter') { document.getElementById('filterForm').submit(); }">
                        </div>
                    </div>

                    <!-- Bouton Rechercher pour la taille ou prix -->
                    <?php if ((!empty($taille_selectionnee) || !empty($prix_min) || !empty($prix_max)) && !$hasActiveFilters): ?>
                    <div class="mt-4">
                        <button type="submit" class="w-full bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition text-sm">
                            Appliquer les filtres
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <script>
            // Toggle filter icon rotation
            document.querySelector('button[onclick*="filterPanel"]').addEventListener('click', function() {
                document.getElementById('filterIcon').classList.toggle('rotate-180');
            });
        </script>

        <!-- le nombre de résultats -->
        <div class="flex items-center justify-between mb-6">
            <p class="text-gray-600">
                <?php if (isset($pagination['total_items'])): ?>
                    <?= $pagination['total_items'] ?> résultat<?= $pagination['total_items'] > 1 ? 's' : '' ?> trouvé<?= $pagination['total_items'] > 1 ? 's' : '' ?>
                <?php else: ?>
                    <?= count($results) ?> résultat<?= count($results) > 1 ? 's' : '' ?> trouvé<?= count($results) > 1 ? 's' : '' ?>
                <?php endif; ?>
            </p>
            <?php if (!empty($query)): ?>
                <div class="flex items-center gap-2 text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Recherche sémantique activée
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- les résultats -->
        <?php if (count($results) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($results as $product): ?>
                <?= view('components/product-card', ['product' => $product]) ?>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?= view('components/pagination', ['pagination' => $pagination]) ?>

        <?php else: ?>
        <!-- les résultats non trouvés -->
        <div class="text-center py-16">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Aucun résultat trouvé</h2>
            <p class="text-gray-500 mb-6">Essayez avec d'autres mots-clés ou filtres</p>
            <a href="/recherche" class="inline-block bg-green-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-green-700 transition">
                Voir tous les produits
            </a>
        </div>
        <?php endif; ?>
    </main>

    <?= view('components/footer') ?>
</body>
</html>
