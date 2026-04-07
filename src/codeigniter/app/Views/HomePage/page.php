<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'OnlyShoes - Vente de chaussures']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
    }
    
    .hero-gradient {
        background: linear-gradient(135deg, #0d9467ff 0%, #34d399 50%, #8cc7abff 100%);
    }
    
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
    
    .search-bar-focus:focus-within {
        box-shadow: 0 8px 30px rgba(16, 185, 129, 0.2);
    }
</style>

<body class="bg-gray-50 overflow-x-hidden">
    <!-- Header -->
    <?= view('components/header') ?>

    <!-- la hero et son CTA bienn incitant à rechercher -->
    <section class="hero-gradient relative overflow-hidden min-h-[85vh] flex items-center">
        <div class="absolute inset-0 opacity-20 bg-[url('https://t4.ftcdn.net/jpg/05/36/51/51/360_F_536515122_Dx9mNFa4dvfPpWjPQqVvkaEfFA3t5Aie.jpg')] bg-cover bg-center">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-emerald-200 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 py-20 relative z-10 w-full">
            <div class="inline-block mb-6 px-4 py-2 bg-white/30 backdrop-blur-md rounded-full">
                <span class="text-sm font-semibold text-gray-900">LA SNEAKER DE VOS RÊVES VOUS ATTEND</span>
            </div>
            
            <h1 class="text-6xl md:text-8xl font-bold text-slate-900 mb-8 leading-tight">
                Trouvez vos<br/>
                <span class="italic">sneakers parfaites</span>
            </h1>

            <!-- Search Bar -->
            <form action="/recherche" method="GET" class="glass-effect rounded-3xl p-3 max-w-3xl shadow-2xl search-bar-focus transition-all">
                <div class="flex items-center gap-4">
                    <!-- Search Input -->
                    <div class="flex-1 flex items-center space-x-3 px-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" name="q" placeholder="Jordan 1, Air Max, Nike Dunk..." class="bg-transparent outline-none w-full font-medium text-gray-900 placeholder-gray-500 text-lg py-2">
                    </div>

                    <!-- Search Button -->
                    <button type="submit" class="bg-gray-900 text-white font-semibold py-4 px-8 rounded-2xl hover:bg-gray-800 transition flex items-center space-x-2 whitespace-nowrap">
                        <span>RECHERCHER</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <p class="text-white/90 text-sm mt-4 max-w-3xl">
                Utilisez des filtres avancés (marque, couleur, état) sur la page de recherche
            </p>
        </div>
    </section>

    <!-- section pour montrer les produits -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Suggestions pour vous</h2>
                <p class="text-gray-600">Découvrez les sneakers les plus populaires du moment</p>
            </div>

            <div class="flex gap-3">
                <a href="/recherche" class="text-sm font-medium text-gray-900 hover:text-green-600 transition">Tout voir</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <?= view('components/product-card', ['product' => $product]) ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- le footer -->
    <?= view('components/footer') ?>

</body>
</html>
