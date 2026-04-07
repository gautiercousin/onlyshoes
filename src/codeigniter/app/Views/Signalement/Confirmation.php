<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => 'Signalement pris en compte - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .glass-effect {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
    }
    
    @keyframes checkmark {
        0% { stroke-dashoffset: 100; }
        100% { stroke-dashoffset: 0; }
    }
    
    @keyframes circle {
        0% { stroke-dashoffset: 166; }
        100% { stroke-dashoffset: 0; }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .success-animation {
        animation: fadeIn 0.6s ease-out;
    }
    
    .checkmark-circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        animation: circle 0.6s ease-in-out 0.2s forwards;
    }
    
    .checkmark-check {
        stroke-dasharray: 100;
        stroke-dashoffset: 100;
        animation: checkmark 0.3s ease-in-out 0.8s forwards;
    }
</style>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Card de confirmation -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center success-animation">
                <!-- Icône de succès animée -->
                <div class="flex justify-center mb-6">
                    <svg class="w-24 h-24" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke="#10b981" stroke-width="2"/>
                        <path class="checkmark-check" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M14 27l7.5 7.5L38 18"/>
                    </svg>
                </div>

                <!-- Titre -->
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Signalement pris en compte
                </h1>

                <!-- Message de confirmation -->
                <p class="text-lg text-gray-600 mb-8">
                    Votre signalement a été enregistré avec succès. Notre équipe de modération va l'examiner dans les plus brefs délais.
                </p>

                <!-- Informations supplémentaires -->
                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8 text-left">
                    <h3 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Prochaines étapes
                    </h3>
                    <ol class="space-y-2 text-sm text-green-800">
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-200 text-green-900 font-bold text-xs mr-3 flex-shrink-0 mt-0.5">1</span>
                            <span>Notre équipe examine votre signalement de manière confidentielle.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-200 text-green-900 font-bold text-xs mr-3 flex-shrink-0 mt-0.5">2</span>
                            <span>Une enquête est menée si nécessaire.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-200 text-green-900 font-bold text-xs mr-3 flex-shrink-0 mt-0.5">3</span>
                            <span>Des mesures appropriées sont prises selon la gravité de la situation.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-200 text-green-900 font-bold text-xs mr-3 flex-shrink-0 mt-0.5">4</span>
                            <span>Vous contribuez à maintenir une communauté saine et respectueuse.</span>
                        </li>
                    </ol>
                </div>

                <!-- Message de remerciement -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-8">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <div class="ml-4 text-left">
                            <h3 class="text-sm font-semibold text-blue-900 mb-1">Merci de votre vigilance</h3>
                            <p class="text-sm text-blue-800">
                                En signalant les contenus inappropriés, vous aidez OnlyShoes à rester une plateforme sûre et agréable pour tous les utilisateurs.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bouton de retour -->
                <div class="space-y-3">
                    <a href="/" 
                       class="inline-block w-full sm:w-auto bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-4 px-8 rounded-2xl transition-all duration-200 shadow-lg shadow-green-600/30 hover:shadow-xl hover:shadow-green-600/40">
                        Retour à l'accueil
                    </a>
                    <p class="text-sm text-gray-500">
                        Vous serez automatiquement redirigé dans <span id="countdown" class="font-semibold text-green-600">5</span> secondes
                    </p>
                </div>
            </div>

            <!-- Note de bas de page -->
            <p class="text-center text-sm text-gray-500 mt-6">
                Pour toute question, contactez notre équipe de support à 
                <a href="mailto:support@onlyshoes.com" class="text-green-600 hover:underline">support@onlyshoes.com</a>
            </p>
        </div>
    </div>

    <script>
        // Compte à rebours et redirection automatique
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = '/';
            }
        }, 1000);

        // Permettre d'annuler la redirection si l'utilisateur interagit
        document.addEventListener('click', () => {
            clearInterval(interval);
            if (countdownElement) {
                countdownElement.parentElement.style.display = 'none';
            }
        });
    </script>
</body>
</html>
