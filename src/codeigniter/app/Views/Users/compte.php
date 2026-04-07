<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => $title ?? 'Mon compte - OnlyShoes']) ?>
<body class="bg-gray-50 overflow-x-hidden">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
        }
    </style>

    <?= view('components/header') ?>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
            <div class="flex items-center gap-6">
                <?= view('components/user-avatar', [
                    'prenom' => $user['prenom'] ?? '',
                    'nom' => $user['nom'] ?? '',
                    'size' => 'w-20 h-20',
                    'textSize' => 'text-2xl',
                    'isAdmin' => ($user['type_compte'] ?? '') === 'admin'
                ]) ?>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Mon compte</h1>
                    <p class="text-gray-500 text-sm">
                        Modifiez vos informations personnelles et vos coordonnées.
                    </p>
                </div>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-2xl mb-6">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-sm p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Informations personnelles</h2>
            </div>

            <form action="<?= base_url('compte') ?>" method="POST" class="space-y-8">
                <!-- Informations de base -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prenom</label>
                        <input type="text" name="prenom" value="<?= esc($user['prenom'] ?? '') ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                        <input type="text" name="nom" value="<?= esc($user['nom'] ?? '') ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?= esc($user['email'] ?? '') ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Changement de mot de passe (optionnel) -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Changer mon mot de passe</h3>
                        <span class="text-xs text-gray-400 italic">(optionnel)</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Laissez vide pour conserver votre mot de passe actuel</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
                            <input type="password" name="mot_de_passe" placeholder="Minimum 8 caractères"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="mot_de_passe_confirmation" placeholder="Retapez le mot de passe"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Adresse de livraison</h3>
                        <span class="text-xs text-gray-400 italic">(optionnel)</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rue 1</label>
                            <input type="text" name="rue1" value="<?= esc($address['rue1'] ?? '') ?>" placeholder="12 rue de l'Exemple"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rue 2</label>
                            <input type="text" name="rue2" value="<?= esc($address['rue2'] ?? '') ?>" placeholder="Appartement, batiment, etage..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Code postal</label>
                            <input type="text" name="code_postal" value="<?= esc($address['code_postal'] ?? '') ?>" placeholder="44000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                            <input type="text" name="ville" value="<?= esc($address['ville'] ?? '') ?>" placeholder="Nantes"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                            <input type="text" name="pays" value="<?= esc($address['pays'] ?? '') ?>" placeholder="France"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Sécurité: confirmer avec mot de passe actuel -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm mb-1">Sécurité</h4>
                                <p class="text-sm text-gray-700">Pour confirmer ces modifications, veuillez entrer votre mot de passe actuel</p>
                            </div>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel <span class="text-red-500">*</span></label>
                        <input type="password" name="mot_de_passe_actuel" placeholder="Votre mot de passe" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="<?= base_url('compte') ?>" class="px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?= view('components/footer') ?>
</body>
</html>
