<?php
    $prenom = $prenom ?? '';
    $nom = $nom ?? '';
    $email = $email ?? '';
    $userId = $userId ?? '';
    $size = $size ?? 'w-14 h-14';
    $extraClasses = $extraClasses ?? '';
    $isAdmin = $isAdmin ?? false;

    $seed = $userId ?: ($email ?: ($prenom . $nom));
    $seed = $seed ?: 'default';
    $avatarUrl = 'https://api.dicebear.com/9.x/thumbs/svg?seed=' . urlencode($seed);
?>

<div class="relative <?= esc($extraClasses) ?>">
    <div class="<?= esc($size) ?> rounded-full overflow-hidden bg-gray-100">
        <img src="<?= esc($avatarUrl) ?>" alt="Avatar" class="w-full h-full object-cover">
    </div>
    <?php if ($isAdmin): ?>
        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </span>
    <?php endif; ?>
</div>
