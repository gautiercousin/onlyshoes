<?php
/**
 * Composant de pagination réutilisable
 *
 * Variables attendues:
 * - $pagination: array avec 'current_page', 'total_pages', 'per_page', 'total_items'
 * - $base_url: URL de base (optionnel, par défaut: URL actuelle sans paramètre page)
 * - $paramName: Nom du paramètre (optionnel, défaut: 'page')
 * - $anchor: Ancre HTML (optionnel, ex: '#reviews')
 */

// Si pas de pagination ou une seule page, ne rien afficher
if (!isset($pagination) || $pagination['total_pages'] <= 1) {
    return;
}

$currentPage = $pagination['current_page'];
$totalPages = $pagination['total_pages'];
$paramName = $paramName ?? 'page';
$anchor = $anchor ?? '';

// Construire l'URL de base (conserver les autres paramètres GET)
$baseUrl = $base_url ?? current_url();
$query = $_GET;
unset($query[$paramName]); // Retirer le paramètre de pagination
$queryString = http_build_query($query);
$separator = $queryString ? '&' : '?';
?>

<div class="flex items-center justify-center gap-2 mt-8">
    <!-- Bouton Précédent -->
    <?php if ($currentPage > 1): ?>
        <a href="<?= $baseUrl . ($queryString ? '?' . $queryString : '') . $separator . $paramName ?>=<?= $currentPage - 1 . $anchor ?>"
           class="px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
            ← Précédent
        </a>
    <?php else: ?>
        <span class="px-4 py-2 bg-gray-100 text-gray-400 border border-gray-200 rounded-xl cursor-not-allowed">
            ← Précédent
        </span>
    <?php endif; ?>

    <!-- Numéros de page -->
    <div class="flex gap-2">
        <?php
        // Afficher au maximum 7 numéros de page
        $maxButtons = 7;
        $startPage = max(1, $currentPage - floor($maxButtons / 2));
        $endPage = min($totalPages, $startPage + $maxButtons - 1);

        // Réajuster si on est près de la fin
        if ($endPage - $startPage < $maxButtons - 1) {
            $startPage = max(1, $endPage - $maxButtons + 1);
        }

        // Toujours afficher la page 1 si elle n'est pas visible
        if ($startPage > 1): ?>
            <a href="<?= $baseUrl . ($queryString ? '?' . $queryString : '') . $separator ?><?= $paramName ?>=1<?= $anchor ?>"
               class="px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                1
            </a>
            <?php if ($startPage > 2): ?>
                <span class="px-4 py-2 text-gray-400">...</span>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Pages visibles -->
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <?php if ($i == $currentPage): ?>
                <span class="px-4 py-2 bg-green-500 text-white font-semibold rounded-xl">
                    <?= $i ?>
                </span>
            <?php else: ?>
                <a href="<?= $baseUrl . ($queryString ? '?' . $queryString : '') . $separator ?><?= $paramName ?>=<?= $i ?><?= $anchor ?>"
                   class="px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                    <?= $i ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- Toujours afficher la dernière page si elle n'est pas visible -->
        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <span class="px-4 py-2 text-gray-400">...</span>
            <?php endif; ?>
            <a href="<?= $baseUrl . ($queryString ? '?' . $queryString : '') . $separator ?><?= $paramName ?>=<?= $totalPages ?><?= $anchor ?>"
               class="px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <?= $totalPages ?>
            </a>
        <?php endif; ?>
    </div>

    <!-- Bouton Suivant -->
    <?php if ($currentPage < $totalPages): ?>
        <a href="<?= $baseUrl . ($queryString ? '?' . $queryString : '') . $separator ?><?= $paramName ?>=<?= $currentPage + 1 ?><?= $anchor ?>"
           class="px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
            Suivant →
        </a>
    <?php else: ?>
        <span class="px-4 py-2 bg-gray-100 text-gray-400 border border-gray-200 rounded-xl cursor-not-allowed">
            Suivant →
        </span>
    <?php endif; ?>
</div>

<!-- Info sur la pagination -->
<div class="text-center text-sm text-gray-500 mt-4">
    Affichage de
    <span class="font-semibold"><?= ($currentPage - 1) * $pagination['per_page'] + 1 ?></span>
    à
    <span class="font-semibold"><?= min($currentPage * $pagination['per_page'], $pagination['total_items']) ?></span>
    sur
    <span class="font-semibold"><?= $pagination['total_items'] ?></span>
    résultats
</div>
