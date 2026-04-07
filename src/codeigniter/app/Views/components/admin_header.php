<!-- Top Bar Admin -->
<div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900"><?= esc($page_title ?? 'Dashboard') ?></h1>
            <p class="text-sm sm:text-base text-gray-500 mt-1"><?= esc($page_subtitle ?? '') ?></p>
        </div>
        <div class="flex items-center space-x-3">
            <?php
                $adminNomComplet = trim($admin_nom ?? '');
                $adminParts = $adminNomComplet !== '' ? preg_split('/\s+/', $adminNomComplet, 2) : [];
                $adminPrenom = $adminParts[0] ?? 'Admin';
                $adminNom = $adminParts[1] ?? '';
            ?>
            <?= view('components/user-avatar', [
                'prenom' => $adminPrenom,
                'nom' => $adminNom,
                'size' => 'w-10 h-10',
                'textSize' => 'text-sm',
                'isAdmin' => true
            ]) ?>
            <div>
                <p class="text-sm font-medium text-gray-900"><?= esc($admin_nom ?? 'Administrateur') ?></p>
                <p class="text-xs text-gray-500">Administrateur</p>
            </div>
        </div>
    </div>
</div>
