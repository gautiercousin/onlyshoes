<header class="glass-effect sticky top-0 z-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <!-- Emplacement logo -->
            <a href="/" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center overflow-hidden">
                    <img src="/rounded-logo.webp" alt="OnlyShoes" class="w-full h-full object-cover">
                </div>
                <span class="text-xl font-bold text-gray-900">OnlyShoes</span>
            </a>

            <!-- Emplacement navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-sm font-medium text-gray-700 hover:text-green-600 transition">Accueil</a>
                <a href="/recherche" class="text-sm font-medium text-gray-700 hover:text-green-600 transition">Acheter</a>
                <a href="/vendre" class="text-sm font-medium text-gray-700 hover:text-green-600 transition">Vendre</a>
            </nav>

            <!-- Emplacement actions utilisateur -->
            <div class="hidden md:flex items-center space-x-4">
                <form action="/" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Rechercher..." 
                           class="w-40 focus:w-56 transition-all pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>
                <?php if (session()->get('is_logged_in')): ?>
                    <?php
                        $prenom = session()->get('user_prenom') ?? '';
                        $nom = session()->get('user_nom') ?? '';
                    ?>
                    <div class="relative">
                        <button id="accountMenuBtn" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-green-600 transition">
                            <?= view('components/user-avatar', [
                                'prenom' => $prenom,
                                'nom' => $nom,
                                'size' => 'w-9 h-9',
                                'textSize' => 'text-sm',
                                'isAdmin' => session()->get('user_type_compte') === 'admin'
                            ]) ?>
                            <span>Mon compte</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="accountMenuDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg z-50">
                            <?php if (session()->get('user_type_compte') === 'admin'): ?>
                                <a href="/admin/dashboard" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Dashboard admin
                                </a>
                                <a href="/commandes" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Mes commandes
                                </a>
                                <a href="/ventes" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Mes ventes
                                </a>
                                <a href="/mes-annonces" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Mes annonces
                                </a>
                                <a href="/compte" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Paramètres du compte
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('utilisateur/profil/' . session()->get('user_id')) ?>" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Voir mon profil
                                </a>
                                <a href="/compte" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Modifier mon compte
                                </a>
                                <a href="/commandes" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Mes commandes
                                </a>
                                <a href="/ventes" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Mes ventes
                                </a>
                                <a href="/mes-annonces" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Mes annonces
                                </a>
                            <?php endif; ?>
                            <a href="/deconnexion" class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Déconnexion
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/connexion" class="text-sm font-medium text-gray-700 hover:text-green-600 transition">Connexion</a>
                    <a href="/inscription" class="bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-green-700 transition">S'inscrire</a>
                <?php endif; ?>
            </div>

            <!-- Emplacement menu mobile -->
            <button id="mobileMenuBtn" class="md:hidden p-2 text-gray-600 hover:text-green-600 transition" aria-label="Menu">
                <svg id="menuIconOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg id="menuIconClose" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Emplacement menu mobile -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100">
        <div class="px-4 py-4 space-y-3">
            <!-- Search form pour les mobile -->
            <form action="/" method="GET" class="relative mb-4">
                <input type="text" name="q" placeholder="Rechercher des sneakers..." 
                       class="w-full pl-10 pr-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </form>
            <hr class="my-2 border-gray-200">
            <a href="/" class="block py-2 text-base font-medium text-gray-900 hover:text-green-600 transition">Accueil</a>
            <a href="/recherche" class="block py-2 text-base font-medium text-gray-900 hover:text-green-600 transition">Acheter</a>
            <a href="/vendre" class="block py-2 text-base font-medium text-gray-900 hover:text-green-600 transition">Vendre</a>
            <hr class="my-3 border-gray-200">
            <?php if (session()->get('is_logged_in')): ?>
                <?php if (session()->get('user_type_compte') === 'admin'): ?>
                    <a href="/admin/dashboard" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Dashboard admin</a>
                    <a href="/commandes" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Mes commandes</a>
                    <a href="/ventes" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Mes ventes</a>
                    <a href="/mes-annonces" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Mes annonces</a>
                    <a href="/compte" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Paramètres du compte</a>
                <?php else: ?>
                    <a href="<?= base_url('utilisateur/profil/' . session()->get('user_id')) ?>" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Voir mon profil</a>
                    <a href="/compte" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Modifier mon compte</a>
                    <a href="/commandes" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Mes commandes</a>
                    <a href="/ventes" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Mes ventes</a>
                    <a href="/mes-annonces" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Mes annonces</a>
                <?php endif; ?>
                <a href="/deconnexion" class="block py-2 text-base font-medium text-red-600 hover:text-red-700 transition">Déconnexion</a>
            <?php else: ?>
                <a href="/connexion" class="block py-2 text-base font-medium text-gray-700 hover:text-green-600 transition">Connexion</a>
                <a href="/inscription" class="block w-full text-center bg-green-600 text-white font-semibold py-3 rounded-xl hover:bg-green-700 transition">S'inscrire</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobileMenu');
        const iconOpen = document.getElementById('menuIconOpen');
        const iconClose = document.getElementById('menuIconClose');
        
        mobileMenu.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        iconClose.classList.toggle('hidden');
    });

    const accountMenuBtn = document.getElementById('accountMenuBtn');
    const accountMenuDropdown = document.getElementById('accountMenuDropdown');

    if (accountMenuBtn && accountMenuDropdown) {
        accountMenuBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            accountMenuDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(event) {
            if (!accountMenuDropdown.contains(event.target) && !accountMenuBtn.contains(event.target)) {
                accountMenuDropdown.classList.add('hidden');
            }
        });
    }
</script>
