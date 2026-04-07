<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Inscription - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .glass-effect {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
    }
</style>
<body class="bg-gray-50 min-h-screen">
    <?= view('components/header') ?>

    <main class="flex items-center justify-center py-16 px-4">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <img src="/rounded-logo.webp" alt="OnlyShoes" class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">Créer un compte</h1>
                    <p class="text-gray-500 mt-2">Rejoignez la communauté OnlyShoes</p>
                </div>

                <!-- Messages flash -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire d'inscription -->
                <form action="/inscription" method="POST" class="space-y-5">
                    <!-- Name -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                            <input type="text" id="prenom" name="prenom" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                                placeholder="Jean">
                        </div>
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                            <input type="text" id="nom" name="nom" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                                placeholder="Dupont">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                            placeholder="votre@email.com">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                            placeholder="••••••••">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirm" name="password_confirm" required minlength="8"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none"
                            placeholder="••••••••">
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-green-600 hover:to-green-700 transition shadow-lg shadow-green-500/30">
                        Créer mon compte
                    </button>
                </form>

                <p class="text-xs text-center text-gray-500 mt-4">
                    En créant un compte, vous acceptez nos <a href="/cgv" class="text-green-600 hover:underline">CGV</a> et notre <a href="/confidentialite" class="text-green-600 hover:underline">politique de confidentialité</a>
                </p>

                <!-- lien retour connexion -->
                <p class="text-center text-gray-600 mt-8">
                    Déjà un compte ? 
                    <a href="/connexion" class="text-green-600 font-semibold hover:underline">Se connecter</a>
                </p>
            </div>
        </div>
    </main>

    <?= view('components/footer') ?>
</body>
</html>
