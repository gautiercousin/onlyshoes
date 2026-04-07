<?php

$type = $type ?? 'user';
$id = $id ?? '';
$position = $position ?? 'right';
$currentUrl = current_url();
$authorId = $authorId ?? null; // Pour les reviews/annonces: ID de l'auteur

// Ne pas afficher si l'utilisateur n'est pas connecté
if (!session()->get('is_logged_in')) {
    return;
}

// Ne pas afficher si c'est son propre contenu
$currentUserId = session()->get('user_id');
if ($type === 'user' && $id === $currentUserId) {
    return; // Ne pas afficher sur son propre profil utilisateur
}
if ($type === 'review' && $authorId && $authorId === $currentUserId) {
    return; // Ne pas afficher sur son propre avis
}
if ($type === 'annonce' && $authorId && $authorId === $currentUserId) {
    return; // Ne pas afficher sur sa propre annonce
}
?>

<div class="relative inline-block text-left report-dropdown">
    <button type="button" 
            class="report-trigger inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors duration-200"
            aria-label="Plus d'options">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
        </svg>
    </button>

    <div class="report-menu hidden absolute <?= $position === 'left' ? 'left-0' : 'right-0' ?> z-10 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5">
        <div class="py-1" role="menu">
            <a href="/signalement/<?= esc($type) ?>/<?= esc($id) ?>?return_url=<?= urlencode($currentUrl) ?>" 
               class="group flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150" 
               role="menuitem">
                <svg class="mr-3 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Signaler <?php
                    switch($type) {
                        case 'user': echo 'ce compte'; break;
                        case 'annonce': echo 'cette annonce'; break;
                        case 'review': echo 'cet avis'; break;
                    }
                ?>
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    // Fonction pour initialiser les dropdowns
    function initReportDropdowns() {
        document.querySelectorAll('.report-dropdown').forEach(dropdown => {
            const trigger = dropdown.querySelector('.report-trigger');
            const menu = dropdown.querySelector('.report-menu');
            
            if (trigger && menu && !trigger.hasAttribute('data-initialized')) {
                trigger.setAttribute('data-initialized', 'true');
                
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Fermer tous les autres menus
                    document.querySelectorAll('.report-menu').forEach(otherMenu => {
                        if (otherMenu !== menu) {
                            otherMenu.classList.add('hidden');
                        }
                    });
                    
                    // Toggle le menu actuel
                    menu.classList.toggle('hidden');
                });
            }
        });
    }
    
    // Fermer les menus en cliquant à l'extérieur
    if (!document.body.hasAttribute('data-report-global-click')) {
        document.body.setAttribute('data-report-global-click', 'true');
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.report-dropdown')) {
                document.querySelectorAll('.report-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    }
    
    // Initialiser immédiatement
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReportDropdowns);
    } else {
        initReportDropdowns();
    }
})();
</script>

<style>
.report-dropdown {
    display: inline-block;
}
</style>
