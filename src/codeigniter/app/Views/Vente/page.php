<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Vendre vos sneakers - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }

    .glass-effect {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
    }

    .step-indicator {
        transition: all 0.3s ease;
    }

    .step-indicator.active {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        transform: scale(1.1);
    }

    .step-indicator.completed {
        background: #10b981;
        color: white;
    }

    .step-content {
        display: none;
        opacity: 0;
    }

    .step-content.active {
        display: block;
        animation: fadeInUp 0.4s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<body class="bg-gradient-to-br from-green-50 via-white to-blue-50 min-h-screen">
    <?= view('components/header') ?>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Vendre vos sneakers</h1>
                <p class="text-lg text-gray-600">Suivez ces étapes simples pour publier votre annonce</p>
            </div>

            <!-- Step Progress -->
            <div class="flex items-center justify-center mb-12">
                <div class="flex items-center space-x-4">
                    <div class="step-indicator active flex items-center justify-center w-12 h-12 rounded-full font-bold" data-step="1">1</div>
                    <div class="w-16 h-1 bg-gray-200 step-line" data-line="1"></div>
                    <div class="step-indicator flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-600 font-bold" data-step="2">2</div>
                    <div class="w-16 h-1 bg-gray-200 step-line" data-line="2"></div>
                    <div class="step-indicator flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-600 font-bold" data-step="3">3</div>
                    <div class="w-16 h-1 bg-gray-200 step-line" data-line="3"></div>
                    <div class="step-indicator flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-600 font-bold" data-step="4">4</div>
                </div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= base_url('vendre') ?>" method="POST" enctype="multipart/form-data" id="venteForm" class="glass-effect rounded-3xl shadow-xl p-8 border border-white/20">
                <?= csrf_field() ?>

                <!-- Step 1: Image Upload -->
                <div class="step-content active" data-step="1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Photo du produit</h2>
                    <p class="text-gray-600 mb-6">Une belle photo attire plus d'acheteurs</p>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-12 text-center hover:border-green-500 transition" id="uploadZone">
                        <input type="file" name="image" id="imageInput" accept="image/*" class="hidden" required>
                        <div id="uploadPrompt">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 mb-2">Cliquez pour ajouter une photo</p>
                            <p class="text-sm text-gray-500">PNG, JPG, WEBP jusqu'à 5MB</p>
                        </div>
                        <div id="imagePreview" style="display: none;">
                            <img id="previewImg" class="mx-auto rounded-xl max-h-96 object-contain mb-4" alt="Aperçu">
                            <button type="button" id="changeImage" class="text-green-600 hover:text-green-700 font-medium">Changer l'image</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Basic Info -->
                <div class="step-content" data-step="2">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Informations de base</h2>
                    <p class="text-gray-600 mb-6">Décrivez votre produit</p>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Titre de l'annonce *</label>
                            <input type="text" name="titre" placeholder="Ex: Nike Air Jordan 1 Retro High OG" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Description *</label>
                            <textarea name="description" rows="4" placeholder="Décrivez l'état, l'histoire, les détails particuliers..." required
                                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Prix (€) *</label>
                            <input type="number" name="prix" step="0.01" min="0" placeholder="150.00" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Step 3: Product Details -->
                <div class="step-content" data-step="3">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Détails du produit</h2>
                    <p class="text-gray-600 mb-6">Informations techniques</p>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Marque *</label>
                            <select name="id_marque" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Sélectionnez une marque</option>
                                <?php foreach ($marques as $marque): ?>
                                    <option value="<?= esc($marque['id_marque']) ?>"><?= esc($marque['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Couleur *</label>
                            <select name="id_couleur" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Sélectionnez une couleur</option>
                                <?php foreach ($couleurs as $couleur): ?>
                                    <option value="<?= esc($couleur['id_couleur']) ?>"><?= esc($couleur['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Matériau *</label>
                            <select name="id_materiau" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Sélectionnez un matériau</option>
                                <?php foreach ($materiaux as $materiau): ?>
                                    <option value="<?= esc($materiau['id_materiau']) ?>"><?= esc($materiau['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Size & Condition -->
                <div class="step-content" data-step="4">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Taille et état</h2>
                    <p class="text-gray-600 mb-6">Derniers détails</p>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Système de taille *</label>
                                <select name="taille_systeme" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="EU">EU</option>
                                    <option value="US">US</option>
                                    <option value="UK">UK</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Taille *</label>
                                <input type="text" name="taille" placeholder="42" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">État *</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="neuf" required class="mr-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">Neuf</div>
                                        <div class="text-sm text-gray-500">Jamais porté</div>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="tres_bon" required class="mr-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">Très bon</div>
                                        <div class="text-sm text-gray-500">Peu porté</div>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="bon" required class="mr-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">Bon</div>
                                        <div class="text-sm text-gray-500">Signes d'usage</div>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-green-500 transition">
                                    <input type="radio" name="etat" value="correct" required class="mr-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">Correct</div>
                                        <div class="text-sm text-gray-500">Bien porté</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                    <button type="button" id="prevBtn" class="px-6 py-3 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition" style="display: none;">
                        ← Précédent
                    </button>
                    <div></div>
                    <button type="button" id="nextBtn" class="px-6 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition shadow-lg">
                        Suivant →
                    </button>
                    <button type="submit" id="submitBtn" class="px-8 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition shadow-lg shadow-green-500/30" style="display: none;">
                        Publier l'annonce
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?= view('components/footer') ?>
    
    <script>
        let currentStep = 1;
        const totalSteps = 4;
        
        const stepContents = document.querySelectorAll('.step-content');
        const stepIndicators = document.querySelectorAll('.step-indicator');
        const stepLines = document.querySelectorAll('.step-line');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('venteForm');
        
        function showStep(step) {
            stepContents.forEach((content, index) => {
                content.classList.remove('active');
                if (index + 1 === step) {
                    content.classList.add('active');
                }
            });
            
            stepIndicators.forEach((indicator, index) => {
                indicator.classList.remove('active', 'completed');
                if (index + 1 === step) {
                    indicator.classList.add('active');
                } else if (index + 1 < step) {
                    indicator.classList.add('completed');
                }
            });
            
            stepLines.forEach((line, index) => {
                if (index + 1 < step) {
                    line.style.background = '#10b981';
                } else {
                    line.style.background = '#e5e7eb';
                }
            });
            
            prevBtn.style.display = step === 1 ? 'none' : 'block';
            nextBtn.style.display = step === totalSteps ? 'none' : 'block';
            submitBtn.style.display = step === totalSteps ? 'block' : 'none';
        }
        
        function validateStep(step) {
            const stepEl = document.querySelector(`.step-content[data-step="${step}"]`);
            if (!stepEl) {
                return true;
            }
            const fields = stepEl.querySelectorAll('input, select, textarea');
            for (const field of fields) {
                if (!field.checkValidity()) {
                    showStep(step);
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        }

        nextBtn.addEventListener('click', () => {
            if (!validateStep(currentStep)) {
                return;
            }
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
        
        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        form.addEventListener('submit', (event) => {
            if (form.checkValidity()) {
                return;
            }

            event.preventDefault();
            const firstInvalid = form.querySelector(':invalid');
            if (!firstInvalid) {
                return;
            }

            const stepEl = firstInvalid.closest('.step-content');
            if (stepEl && stepEl.dataset.step) {
                showStep(Number(stepEl.dataset.step));
            }
            firstInvalid.reportValidity();
        });
        
        // Image upload handling
        const uploadZone = document.getElementById('uploadZone');
        const imageInput = document.getElementById('imageInput');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const changeImage = document.getElementById('changeImage');
        
        uploadZone.addEventListener('click', () => imageInput.click());
        
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    uploadPrompt.style.display = 'none';
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        
        changeImage.addEventListener('click', (e) => {
            e.stopPropagation();
            imageInput.click();
        });
    </script>
</body>
</html>
