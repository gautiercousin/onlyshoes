<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Modifier l\'annonce - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .image-preview {
        display: none;
    }

    .image-preview.active {
        display: block;
    }

    .upload-zone {
        border: 2px dashed #d1d5db;
        transition: all 0.3s ease;
    }

    .upload-zone:hover {
        border-color: #10b981;
        background: #f0fdf4;
    }
</style>

<body class="bg-gradient-to-br from-gray-50 to-green-50 min-h-screen">
    <?= view('components/header') ?>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="/mes-annonces" class="text-green-600 hover:text-green-700 font-medium mb-4 inline-block">
                    ← Retour à mes annonces
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Modifier l'annonce</h1>
                <p class="text-gray-600 mt-2">Mettez à jour les informations de votre produit</p>
            </div>

            <!-- Messages flash -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="glass-effect mb-6 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl shadow-lg">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="/modifier-annonce/<?= esc($annonce['id_annonce']) ?>" method="POST" enctype="multipart/form-data" class="glass-effect rounded-2xl shadow-xl p-8 border border-white/50">
                <?= csrf_field() ?>

                <!-- Image actuelle et upload -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Photo du produit</h2>
                    
                    <?php if (!empty($annonce['image_url'])): ?>
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-3">Image actuelle :</p>
                            <img src="<?= esc($annonce['image_url']) ?>"
                                 alt="Image actuelle"
                                 class="w-48 h-48 object-cover rounded-xl shadow-md border border-gray-200"
                                 onerror="this.src='/notfound.webp'">
                        </div>
                    <?php endif; ?>
                    
                    <div class="upload-zone rounded-2xl p-8 text-center cursor-pointer" id="uploadZone">
                        <input type="file" name="image" id="imageInput" accept="image/*" class="hidden">
                        <div id="uploadPrompt">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-900">Cliquez pour changer l'image (optionnel)</p>
                            <p class="text-xs text-gray-500">PNG, JPG, WEBP jusqu'à 5MB</p>
                        </div>
                        <div id="imagePreview" class="image-preview">
                            <img id="previewImg" class="mx-auto rounded-xl max-h-64 object-contain" alt="Aperçu">
                        </div>
                    </div>
                </div>

                <!-- Informations de base -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informations de base</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Titre de l'annonce</label>
                            <input type="text" name="titre" value="<?= esc($annonce['titre']) ?>" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
                            <textarea name="description" rows="4" 
                                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required><?= esc($annonce['description']) ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Prix (€)</label>
                            <input type="number" name="prix" step="0.01" min="0" value="<?= esc($annonce['prix']) ?>" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        </div>
                    </div>
                </div>

                <!-- Détails du produit -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Détails du produit</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Marque</label>
                            <select name="id_marque" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="">Sélectionnez une marque</option>
                                <?php foreach ($marques as $marque): ?>
                                    <option value="<?= esc($marque['id_marque']) ?>" <?= $marque['id_marque'] == $annonce['id_marque'] ? 'selected' : '' ?>>
                                        <?= esc($marque['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Couleur</label>
                            <select name="id_couleur" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="">Sélectionnez une couleur</option>
                                <?php foreach ($couleurs as $couleur): ?>
                                    <option value="<?= esc($couleur['id_couleur']) ?>" <?= $couleur['id_couleur'] == $annonce['id_couleur'] ? 'selected' : '' ?>>
                                        <?= esc($couleur['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Matériau</label>
                            <select name="id_materiau" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="">Sélectionnez un matériau</option>
                                <?php foreach ($materiaux as $materiau): ?>
                                    <option value="<?= esc($materiau['id_materiau']) ?>" <?= $materiau['id_materiau'] == $annonce['id_materiau'] ? 'selected' : '' ?>>
                                        <?= esc($materiau['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Taille et état -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Taille et état</h2>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Système de taille</label>
                                <select name="taille_systeme" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                    <option value="EU" <?= $annonce['taille_systeme'] == 'EU' ? 'selected' : '' ?>>EU</option>
                                    <option value="US" <?= $annonce['taille_systeme'] == 'US' ? 'selected' : '' ?>>US</option>
                                    <option value="UK" <?= $annonce['taille_systeme'] == 'UK' ? 'selected' : '' ?>>UK</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Taille</label>
                                <input type="text" name="taille" value="<?= esc($annonce['taille']) ?>" 
                                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">État</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border-2 <?= $annonce['etat'] == 'neuf' ? 'border-green-500 bg-green-50' : 'border-gray-300' ?> rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="neuf" <?= $annonce['etat'] == 'neuf' ? 'checked' : '' ?> class="mr-3" required>
                                    <div>
                                        <div class="font-semibold text-gray-900">Neuf</div>
                                        <div class="text-sm text-gray-500">Jamais porté</div>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border-2 <?= $annonce['etat'] == 'tres_bon' ? 'border-green-500 bg-green-50' : 'border-gray-300' ?> rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="tres_bon" <?= $annonce['etat'] == 'tres_bon' ? 'checked' : '' ?> class="mr-3" required>
                                    <div>
                                        <div class="font-semibold text-gray-900">Très bon</div>
                                        <div class="text-sm text-gray-500">Peu porté</div>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border-2 <?= $annonce['etat'] == 'bon' ? 'border-green-500 bg-green-50' : 'border-gray-300' ?> rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="bon" <?= $annonce['etat'] == 'bon' ? 'checked' : '' ?> class="mr-3" required>
                                    <div>
                                        <div class="font-semibold text-gray-900">Bon</div>
                                        <div class="text-sm text-gray-500">Signes d'usage</div>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border-2 <?= $annonce['etat'] == 'correct' ? 'border-green-500 bg-green-50' : 'border-gray-300' ?> rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="correct" <?= $annonce['etat'] == 'correct' ? 'checked' : '' ?> class="mr-3" required>
                                    <div>
                                        <div class="font-semibold text-gray-900">Correct</div>
                                        <div class="text-sm text-gray-500">Bien porté</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4">
                    <a href="/mes-annonces" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 text-center font-semibold rounded-xl hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 transition">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?= view('components/footer') ?>

    <script>
        const uploadZone = document.getElementById('uploadZone');
        const imageInput = document.getElementById('imageInput');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        uploadZone.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    uploadPrompt.style.display = 'none';
                    imagePreview.classList.add('active');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
