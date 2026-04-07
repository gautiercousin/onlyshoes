<?php
/**
 * Composant carte produit (annonce)
 *
 * Variables attendues depuis le modèle AnnoncesModel:
 * - id_annonce, titre, prix, taille, etat, image_url, marque_nom
 */
helper('format');
?>
<a href="<?= base_url('produit/' . $product['id_annonce']) ?>" class="bg-white rounded-3xl overflow-hidden shadow-sm shoe-card cursor-pointer group block">
    <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden">
        <img src="<?= esc($product['image_url'] ?? '/notfound.webp') ?>"
             alt="<?= esc($product['titre']) ?>"
             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
             onerror="this.src='/notfound.webp'">
    </div>
    <div class="p-5">
        <div class="flex items-start justify-between mb-2">
            <div>
                <p class="text-sm text-gray-500 font-medium"><?= esc($product['marque_nom'] ?? 'Marque') ?></p>
                <h3 class="text-lg font-bold text-gray-900"><?= esc($product['titre']) ?></h3>
            </div>
        </div>
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
            <span class="text-sm font-semibold text-green-600"><?= number_format($product['prix'], 2, ',', ' ') ?>€</span>
            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full"><?= esc(format_etat($product['etat'])) ?></span>
        </div>
    </div>
</a>