<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => ($title ?? 'Modifier mon avis') . ' - OnlyShoes']) ?>
<body class="bg-gray-50">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
        }
    </style>

    <?= view('components/header') ?>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <div class="bg-white rounded-3xl shadow-sm p-8">
            <div class="mb-8">
                <a href="<?= esc($redirect ?? base_url('commandes')) ?>" class="text-green-600 hover:text-green-700 text-sm font-medium flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour
                </a>

                <h1 class="text-3xl font-bold text-gray-900">Modifier mon avis</h1>
                <p class="text-gray-600 mt-2">
                    Avis pour
                    <span class="font-semibold text-gray-900">
                        <?= esc($vendeur['prenom']) ?> <?= esc($vendeur['nom']) ?>
                    </span>
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Publié le <?= date('d/m/Y à H:i', strtotime($avis['date'])) ?>
                </p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('review/update/' . $avis['id_review']) ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="<?= esc($redirect ?? base_url('commandes')) ?>">

                <!-- Note -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Note <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2" id="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" name="note" value="<?= $i ?>" id="star-<?= $i ?>" class="sr-only" <?= $i === (int)$avis['note'] ? 'checked' : '' ?> required>
                            <label for="star-<?= $i ?>" class="cursor-pointer star-label" data-rating="<?= $i ?>">
                                <svg class="w-10 h-10 text-gray-300 transition duration-150" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Cliquez pour modifier la note</p>
                </div>

                <!-- Commentaire -->
                <div>
                    <label for="commentaire" class="block text-sm font-medium text-gray-700 mb-2">
                        Commentaire (optionnel)
                    </label>
                    <textarea id="commentaire"
                              name="commentaire"
                              rows="5"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                              placeholder="Partagez votre expérience avec ce vendeur..."><?= esc($avis['commentaire']) ?></textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Enregistrer les modifications
                    </button>
                    <a href="<?= esc($redirect ?? base_url('commandes')) ?>"
                       class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                        Annuler
                    </a>
                </div>
            </form>

            <!-- Supprimer l'avis -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Zone de danger</h3>
                <p class="text-sm text-gray-600 mb-4">
                    La suppression de votre avis est définitive et ne peut pas être annulée.
                </p>
                <form action="<?= base_url('review/supprimer/' . $avis['id_review']) ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ? Cette action est irréversible.');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect" value="<?= esc($redirect ?? base_url('commandes')) ?>">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                        Supprimer l'avis
                    </button>
                </form>
            </div>
        </div>
    </main>

    <?= view('components/footer') ?>

    <script>
        // Star rating hover effect
        const starLabels = document.querySelectorAll('.star-label');
        const starRatingContainer = document.getElementById('star-rating');

        // Get current selected rating
        function getCurrentRating() {
            const checkedInput = document.querySelector('input[name="note"]:checked');
            return checkedInput ? parseInt(checkedInput.value) : 0;
        }

        // Update star colors
        function updateStars(rating) {
            starLabels.forEach((label, index) => {
                const svg = label.querySelector('svg');
                if (index < rating) {
                    svg.classList.remove('text-gray-300');
                    svg.classList.add('text-yellow-400');
                } else {
                    svg.classList.remove('text-yellow-400');
                    svg.classList.add('text-gray-300');
                }
            });
        }

        // Initialize stars with current rating
        updateStars(getCurrentRating());

        // Hover effect
        starLabels.forEach((label) => {
            label.addEventListener('mouseenter', () => {
                const rating = parseInt(label.dataset.rating);
                updateStars(rating);
            });
        });

        // Reset to selected rating on mouse leave
        starRatingContainer.addEventListener('mouseleave', () => {
            updateStars(getCurrentRating());
        });

        // Update on click
        starLabels.forEach((label) => {
            label.addEventListener('click', () => {
                // Small delay to let the radio button update
                setTimeout(() => {
                    updateStars(getCurrentRating());
                }, 10);
            });
        });
    </script>
</body>
</html>
