<!-- Sidebar Admin -->
<aside class="w-64 bg-gradient-to-b from-green-600 to-green-700 text-white min-h-screen fixed lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out z-40 max-w-full overflow-y-auto">
    <div class="p-6 border-b border-green-500">
        <a href="/admin" class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <span class="text-xl font-bold truncate">Admin Panel</span>
        </a>
    </div>

    <nav class="p-4 space-y-2">
        <a href="/" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-green-500 hover:bg-opacity-20 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Retour au site</span>
        </a> 

        <!-- Dashboard -->
        <a href="/admin/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?= ($current_page ?? '') === 'dashboard' ? 'bg-green-500 bg-opacity-40' : 'hover:bg-green-500 hover:bg-opacity-20' ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Utilisateurs -->
        <a href="/admin/utilisateurs" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?= ($current_page ?? '') === 'utilisateurs' ? 'bg-green-500 bg-opacity-40' : 'hover:bg-green-500 hover:bg-opacity-20' ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="font-medium">Utilisateurs</span>
        </a>

        <!-- Signalements -->
        <a href="/admin/signalements" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?= ($current_page ?? '') === 'signalements' ? 'bg-green-500 bg-opacity-40' : 'hover:bg-green-500 hover:bg-opacity-20' ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="font-medium">Signalements</span>
            <?php if (isset($signalements_en_attente) && $signalements_en_attente > 0): ?>
                <span class="ml-auto text-xs bg-red-500 text-white px-2 py-1 rounded-full font-bold"><?= number_format($signalements_en_attente ?? 0) ?></span>
            <?php endif; ?>
        </a>

        <!-- Attributs Produits -->
        <a href="/admin/attributs" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?= ($current_page ?? '') === 'attributs' ? 'bg-green-500 bg-opacity-40' : 'hover:bg-green-500 hover:bg-opacity-20' ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <span class="font-medium">Attributs Produits</span>
        </a>

        <!-- Paramètres -->
        <a href="/compte" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?= ($current_page ?? '') === 'parametres' ? 'bg-green-500 bg-opacity-40' : 'hover:bg-green-500 hover:bg-opacity-20' ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="font-medium">Paramètres</span>
        </a>
    </nav>

    <!-- Déconnexion -->
    <div class="absolute bottom-0 w-64 p-4 border-t border-green-500">
        <form action="/admin/logout" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-green-500 bg-opacity-20 hover:bg-opacity-30 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="font-medium">Se déconnecter</span>
            </button>
        </form>
    </div>
</aside>
