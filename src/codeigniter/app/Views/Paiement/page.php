<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => ($title ?? 'Paiement') . ' - OnlyShoes']) ?>
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

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Paiement</h1>
            <?php
                $annoncePrix = isset($annonce['prix']) ? number_format((float) $annonce['prix'], 2, ',', ' ') . ' €' : '--,-- €';
            ?>
            <p class="text-gray-500">
                Finalisez votre achat pour l'annonce <span class="font-semibold"><?= esc($annonce['titre'] ?? 'Annonce') ?></span>.
            </p>
        </div>

        <form action="/paiement/<?= esc($id_annonce) ?>" method="POST" id="paymentForm">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <section class="bg-white rounded-3xl shadow-sm p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Informations de livraison</h2>

                    <?php if (!$address): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Adresse manquante</h4>
                                    <p class="text-sm text-gray-700">Vous devez d'abord ajouter une adresse de livraison dans <a href="/compte" class="text-green-600 hover:underline">votre compte</a>.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse (rue1) <span class="text-red-500">*</span></label>
                            <input type="text" name="rue1" value="<?= esc($address['rue1'] ?? '') ?>" placeholder="12 rue de l'Exemple" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Complément (rue2)</label>
                            <input type="text" name="rue2" value="<?= esc($address['rue2'] ?? '') ?>" placeholder="Appartement, bâtiment, étage..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Code postal <span class="text-red-500">*</span></label>
                            <input type="text" name="code_postal" value="<?= esc($address['code_postal'] ?? '') ?>" placeholder="44000" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville <span class="text-red-500">*</span></label>
                            <input type="text" name="ville" value="<?= esc($address['ville'] ?? '') ?>" placeholder="Nantes" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays <span class="text-red-500">*</span></label>
                            <input type="text" name="pays" value="<?= esc($address['pays'] ?? '') ?>" placeholder="France" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Paiement <span class="text-red-500">*</span></h2>
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-green-500 transition payment-option">
                            <input type="radio" name="type_paiement" value="carte_bancaire" class="text-green-600 payment-radio">
                            <span class="font-medium text-gray-900">Carte bancaire</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-green-500 transition payment-option">
                            <input type="radio" name="type_paiement" value="paypal" class="text-green-600 payment-radio">
                            <span class="font-medium text-gray-900">PayPal</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-green-500 transition payment-option">
                            <input type="radio" name="type_paiement" value="google_pay" class="text-green-600 payment-radio">
                            <span class="font-medium text-gray-900">Google Pay</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-green-500 transition payment-option">
                            <input type="radio" name="type_paiement" value="apple_pay" class="text-green-600 payment-radio">
                            <span class="font-medium text-gray-900">Apple Pay</span>
                        </label>
                    </div>
                </section>
            </div>

            <aside class="bg-white rounded-3xl shadow-sm p-8 h-fit">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Récapitulatif</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Annonce</span>
                        <span><?= esc($annonce['titre'] ?? 'Annonce') ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Montant</span>
                        <span><?= esc($annoncePrix) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Statut paiement</span>
                        <span>En attente</span>
                    </div>
                    <div class="border-t border-gray-200 pt-4 flex items-center justify-between font-semibold text-gray-900">
                        <span>Total</span>
                        <span><?= esc($annoncePrix) ?></span>
                    </div>
                </div>
                <button type="submit" id="paymentButton" disabled class="mt-6 w-full bg-gray-400 text-white font-semibold py-3 rounded-xl cursor-not-allowed transition">
                    Confirmer le paiement
                </button>
                <p class="text-xs text-gray-500 mt-2 text-center" id="paymentHint">
                    Veuillez sélectionner un mode de paiement
                </p>
            </aside>
        </div>
        </form>
    </main>

    <script>
        // Gestion de l'activation du bouton de paiement
        const paymentRadios = document.querySelectorAll('.payment-radio');
        const paymentButton = document.getElementById('paymentButton');
        const paymentHint = document.getElementById('paymentHint');
        const paymentOptions = document.querySelectorAll('.payment-option');

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    // Activer le bouton
                    paymentButton.disabled = false;
                    paymentButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    paymentButton.classList.add('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');

                    // Masquer le message d'indication
                    paymentHint.style.display = 'none';

                    // Mettre en surbrillance l'option sélectionnée
                    paymentOptions.forEach(option => {
                        option.classList.remove('border-green-500', 'bg-green-50');
                    });
                    this.closest('.payment-option').classList.add('border-green-500', 'bg-green-50');
                }
            });
        });
    </script>

    <?= view('components/footer') ?>
</body>
</html>
