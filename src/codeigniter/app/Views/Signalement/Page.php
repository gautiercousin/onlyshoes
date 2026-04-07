<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Signaler - OnlyShoes']) ?>
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

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Titre de la page -->
            <div class="mb-8">
                <a href="<?= esc($return_url ?? '/') ?>" class="inline-flex items-center text-green-600 hover:text-green-700 mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900">
                    Signaler <?php
                        switch($type) {
                            case 'user': echo 'un compte'; break;
                            case 'annonce': echo 'une annonce'; break;
                            case 'review': echo 'un avis'; break;
                        }
                    ?>
                </h1>
                <p class="text-gray-600 mt-2">
                    <?php
                        switch($type) {
                            case 'user': 
                                echo 'Signalez un compte pour comportement inapproprié, arnaque ou spam.';
                                break;
                            case 'annonce': 
                                echo 'Signalez une annonce pour contenu inapproprié, prix trompeur ou produit contrefait.';
                                break;
                            case 'review': 
                                echo 'Signalez un avis pour contenu inapproprié, spam ou faux avis.';
                                break;
                        }
                    ?>
                </p>
            </div>

            <!-- Card du contenu signalé -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border border-gray-200">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
                    <?php
                        switch($type) {
                            case 'user': echo 'Compte à signaler'; break;
                            case 'annonce': echo 'Annonce à signaler'; break;
                            case 'review': echo 'Avis à signaler'; break;
                        }
                    ?>
                </h2>
                
                <!-- Aperçu selon le type -->
                <?php if ($type === 'user'): ?>
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100">
                            <img src="https://api.dicebear.com/9.x/thumbs/svg?seed=<?= urlencode($cible['id_utilisateur'] ?? ($cible['email'] ?? 'default')) ?>" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                <?= esc($cible['prenom']) ?> <?= esc($cible['nom']) ?>
                            </h3>
                            <p class="text-gray-500"><?= esc($cible['email']) ?></p>
                        </div>
                    </div>
                <?php elseif ($type === 'annonce'): ?>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            <?= esc($cible['titre']) ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-2"><?= esc($cible['description']) ?></p>
                        <p class="text-green-600 font-bold"><?= number_format($cible['prix'], 2) ?> €</p>
                    </div>
                <?php elseif ($type === 'review'): ?>
                    <div>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                <?php for($i = 0; $i < $cible['note']; $i++): ?>
                                    ★
                                <?php endfor; ?>
                            </div>
                            <span class="text-gray-600 text-sm"><?= esc($cible['date']) ?></span>
                        </div>
                        <p class="text-gray-700"><?= esc($cible['commentaire']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Messages flash -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire de signalement -->
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <form action="/signalement/create" method="POST" class="space-y-6">
                    <!-- Hidden fields -->
                    <input type="hidden" name="type" value="<?= esc($type) ?>">
                    <input type="hidden" name="id_cible" value="<?= esc($id_cible) ?>">
                    <input type="hidden" name="return_url" value="<?= esc($return_url ?? '/') ?>">

                    <!-- Sélection du motif -->
                    <div>
                        <label for="motif" class="block text-sm font-semibold text-gray-700 mb-3">
                            Motif du signalement <span class="text-red-500">*</span>
                        </label>
                        <select id="motif" name="motif" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none">
                            <option value="">Sélectionnez un motif...</option>
                            <?php if ($type === 'user'): ?>
                                <option value="comportement_inapproprie">Comportement inapproprié</option>
                                <option value="arnaque">Arnaque</option>
                                <option value="usurpation_identite">Usurpation d'identité</option>
                                <option value="spam">Spam</option>
                            <?php elseif ($type === 'annonce'): ?>
                                <option value="contenu_inapproprie">Contenu inapproprié</option>
                                <option value="prix_trompeur">Prix trompeur</option>
                                <option value="produit_contrefait">Produit contrefait</option>
                                <option value="description_trompeuse">Description trompeuse</option>
                            <?php elseif ($type === 'review'): ?>
                                <option value="contenu_inapproprie">Contenu inapproprié</option>
                                <option value="spam">Spam</option>
                                <option value="faux_avis">Faux avis</option>
                                <option value="harcelement">Harcèlement</option>
                            <?php endif; ?>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-3">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition outline-none resize-none"
                            placeholder="Décrivez en détail la raison de votre signalement..."></textarea>
                        <p class="text-gray-500 text-sm mt-2">
                            Expliquez pourquoi vous signalez <?php
                                switch($type) {
                                    case 'user': echo 'ce compte'; break;
                                    case 'annonce': echo 'cette annonce'; break;
                                    case 'review': echo 'cet avis'; break;
                                }
                            ?>. Soyez aussi précis que possible.
                        </p>
                    </div>

                    <!-- Avertissement -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex">
                            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-yellow-800">Attention</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Les signalements abusifs peuvent entraîner des sanctions sur votre compte. 
                                    Assurez-vous que votre signalement est légitime et bien motivé.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Confirmer le signalement
                        </button>
                        <a href="<?= esc($return_url ?? '/') ?>"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>

            <!-- Informations supplémentaires -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3">Que se passe-t-il après mon signalement ?</h3>
                <ol class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">1</span>
                        <span>Votre signalement est enregistré dans notre système de manière confidentielle.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">2</span>
                        <span>Notre équipe d'administration est automatiquement notifiée.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">3</span>
                        <span>Un modérateur examine votre signalement dans les plus brefs délais.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">4</span>
                        <span>Des mesures appropriées sont prises si le signalement est justifié.</span>
                    </li>
                </ol>
            </div>
        </div>
    </main>

    <?= view('components/footer') ?>
</body>
</html>