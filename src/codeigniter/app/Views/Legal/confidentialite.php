<!DOCTYPE html>
<html lang="fr">
<?= view('components/head', ['title' => $title . ' - OnlyShoes']) ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .glass-effect {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
    }
</style>
<body class="bg-gradient-to-br from-green-50 via-white to-blue-50 min-h-screen">
    <?= view('components/header') ?>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="glass-effect rounded-3xl shadow-xl p-8 md:p-12 border border-white/20">
            <h1 class="text-4xl font-bold text-gray-900 mb-6">Politique de confidentialité</h1>
            <p class="text-sm text-gray-500 mb-8">Dernière mise à jour : <?= date('d/m/Y') ?></p>

            <div class="prose prose-lg max-w-none">
                <!-- Introduction -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Introduction</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        OnlyShoes s'engage à protéger la vie privée de ses utilisateurs. Cette politique de confidentialité explique comment nous collectons, utilisons, partageons et protégeons vos données personnelles conformément au Règlement Général sur la Protection des Données (RGPD).
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        En utilisant notre plateforme, vous acceptez les pratiques décrites dans cette politique.
                    </p>
                </section>

                <!-- Responsable du traitement -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Responsable du traitement</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Le responsable du traitement de vos données personnelles est OnlyShoes.<br>
                        Pour toute question concernant vos données, vous pouvez nous contacter à : <a href="mailto:privacy@onlyshoes.fr" class="text-green-600 hover:underline">privacy@onlyshoes.fr</a>
                    </p>
                </section>

                <!-- Données collectées -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Données collectées</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Nous collectons les données suivantes :</p>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">3.1 Données d'inscription</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Nom et prénom</li>
                        <li>Adresse email</li>
                        <li>Mot de passe (crypté avec bcrypt)</li>
                        <li>Date de création du compte</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">3.2 Données d'adresse</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Adresse postale complète</li>
                        <li>Code postal et ville</li>
                        <li>Pays</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">3.3 Données de transaction</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Historique des commandes et ventes</li>
                        <li>Informations de paiement (traitées par des prestataires sécurisés)</li>
                        <li>Avis et notes laissés</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">3.4 Données de navigation</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Cookies (avec votre consentement)</li>
                        <li>Adresse IP</li>
                        <li>Type de navigateur et appareil</li>
                        <li>Pages visitées</li>
                    </ul>
                </section>

                <!-- Finalités du traitement -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Finalités du traitement</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Vos données sont utilisées pour :</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Créer et gérer votre compte</li>
                        <li>Traiter vos transactions (achats et ventes)</li>
                        <li>Communiquer avec vous (emails de confirmation, notifications)</li>
                        <li>Améliorer nos services (analyses anonymisées)</li>
                        <li>Respecter nos obligations légales</li>
                        <li>Prévenir la fraude et assurer la sécurité</li>
                    </ul>
                </section>

                <!-- Base légale -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Base légale du traitement</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Nous traitons vos données sur les bases légales suivantes :</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li><strong>Exécution du contrat :</strong> pour gérer vos achats/ventes</li>
                        <li><strong>Consentement :</strong> pour les cookies non essentiels, marketing</li>
                        <li><strong>Obligation légale :</strong> conservation des données de facturation (10 ans)</li>
                        <li><strong>Intérêt légitime :</strong> prévention de la fraude, amélioration des services</li>
                    </ul>
                </section>

                <!-- Partage des données -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Partage et monétisation des données</h2>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">6.1 Partage avec nos prestataires</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Vos données peuvent être partagées avec nos prestataires de services :
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li><strong>Prestataires de paiement :</strong> pour traiter les transactions (Stripe, PayPal, etc.)</li>
                        <li><strong>Services d'hébergement :</strong> pour stocker les données de manière sécurisée</li>
                        <li><strong>Autorités légales :</strong> en cas d'obligation légale</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">6.2 Monétisation des données (intérêt légitime)</h3>
                    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-4">
                        <p class="text-gray-700 leading-relaxed mb-3">
                            <strong>Important :</strong> OnlyShoes monétise certaines de vos données en les partageant avec des partenaires tiers. Cette pratique nous permet d'offrir nos services à des tarifs compétitifs.
                        </p>
                    </div>

                    <p class="text-gray-700 leading-relaxed mb-4"><strong>Données concernées :</strong></p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Données de navigation et comportementales (pages visitées, recherches effectuées)</li>
                        <li>Données démographiques (ville, tranche d'âge, centre d'intérêt)</li>
                        <li>Historique d'achats anonymisé (produits achetés/vendus, montants)</li>
                        <li>Préférences produits (marques, couleurs, tailles consultées)</li>
                    </ul>

                    <p class="text-gray-700 leading-relaxed mb-4"><strong>Types de partenaires :</strong></p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li><strong>Régies publicitaires :</strong> pour de la publicité ciblée</li>
                        <li><strong>Plateformes d'analyse :</strong> pour des études de marché</li>
                        <li><strong>Agrégateurs de données :</strong> pour des statistiques sectorielles</li>
                        <li><strong>Partenaires marketing :</strong> pour des campagnes personnalisées</li>
                    </ul>

                    <p class="text-gray-700 leading-relaxed mb-4"><strong>Base légale :</strong></p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Ce traitement repose sur notre <strong>intérêt légitime</strong> à financer nos services. Conformément au RGPD, vous disposez du droit de vous opposer à cette monétisation à tout moment.
                    </p>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                        <h4 class="font-semibold text-gray-900 mb-2">🛡️ Comment refuser la monétisation ?</h4>
                        <p class="text-gray-700 text-sm mb-2">
                            Vous pouvez vous opposer à la monétisation de vos données en nous contactant à <a href="mailto:privacy@onlyshoes.fr" class="text-green-600 hover:underline font-semibold">privacy@onlyshoes.fr</a> avec l'objet "Opposition monétisation".
                        </p>
                        <p class="text-gray-700 text-sm">
                            <strong>Délai de traitement :</strong> 7 jours ouvrés maximum. Vos données ne seront plus partagées avec nos partenaires après ce délai.
                        </p>
                    </div>

                    <p class="text-gray-700 leading-relaxed mt-4">
                        <strong>Note :</strong> Tous nos prestataires et partenaires sont contractuellement tenus de respecter le RGPD et ne peuvent utiliser vos données qu'aux fins spécifiées.
                    </p>
                </section>

                <!-- Durée de conservation -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Durée de conservation</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li><strong>Compte actif :</strong> pendant toute la durée d'utilisation</li>
                        <li><strong>Compte inactif :</strong> 3 ans après la dernière connexion</li>
                        <li><strong>Données de facturation :</strong> 10 ans (obligation légale)</li>
                        <li><strong>Cookies :</strong> 13 mois maximum</li>
                        <li><strong>Logs de connexion :</strong> 12 mois</li>
                    </ul>
                </section>

                <!-- Vos droits RGPD -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Vos droits (RGPD)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Conformément au RGPD, vous disposez des droits suivants :</p>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Droit d'accès</h3>
                        <p class="text-gray-700 text-sm">Obtenir une copie de toutes vos données personnelles</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Droit de rectification</h3>
                        <p class="text-gray-700 text-sm">Corriger des données inexactes ou incomplètes</p>
                    </div>

                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Droit à l'effacement ("droit à l'oubli")</h3>
                        <p class="text-gray-700 text-sm">Supprimer votre compte et vos données (sauf obligation légale de conservation)</p>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Droit à la limitation</h3>
                        <p class="text-gray-700 text-sm">Geler temporairement le traitement de vos données</p>
                    </div>

                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Droit à la portabilité</h3>
                        <p class="text-gray-700 text-sm">Recevoir vos données dans un format structuré (JSON/CSV)</p>
                    </div>

                    <div class="bg-gray-50 border-l-4 border-gray-500 p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Droit d'opposition</h3>
                        <p class="text-gray-700 text-sm">Refuser le traitement de vos données à des fins marketing</p>
                    </div>

                    <p class="text-gray-700 leading-relaxed mt-4">
                        Pour exercer vos droits, contactez-nous à <a href="mailto:privacy@onlyshoes.fr" class="text-green-600 hover:underline">privacy@onlyshoes.fr</a><br>
                        Nous répondrons sous 1 mois maximum.
                    </p>
                </section>

                <!-- Cookies -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Cookies</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Nous utilisons des cookies pour :</p>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">9.1 Cookies essentiels (obligatoires)</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Session utilisateur (connexion)</li>
                        <li>Panier d'achat</li>
                        <li>Sécurité CSRF</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">9.2 Cookies de performance (avec consentement)</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Google Analytics (statistiques anonymisées)</li>
                        <li>Analyse des performances du site</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">9.3 Cookies marketing (avec consentement)</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Publicité ciblée</li>
                        <li>Réseaux sociaux</li>
                    </ul>

                    <p class="text-gray-700 leading-relaxed">
                        Vous pouvez gérer vos préférences de cookies à tout moment via notre <a href="/cookies/preferences" class="text-green-600 hover:underline">centre de préférences</a>.
                    </p>
                </section>

                <!-- Sécurité -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Sécurité</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Nous mettons en œuvre des mesures techniques et organisationnelles pour protéger vos données :</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Chiffrement des mots de passe (bcrypt)</li>
                        <li>HTTPS (SSL/TLS) pour toutes les communications</li>
                        <li>Pare-feu et systèmes de détection d'intrusion</li>
                        <li>Sauvegardes régulières chiffrées</li>
                        <li>Accès restreint aux données (principe du moindre privilège)</li>
                        <li>Audits de sécurité réguliers</li>
                    </ul>
                </section>

                <!-- Modifications -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Modifications de cette politique</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Nous pouvons modifier cette politique de confidentialité à tout moment. Les modifications seront publiées sur cette page avec une nouvelle date de mise à jour. En cas de changement majeur, nous vous informerons par email.
                    </p>
                </section>

                <!-- Contact -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Contact</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Pour toute question concernant cette politique ou vos données personnelles :<br>
                        <strong>Email :</strong> <a href="mailto:privacy@onlyshoes.fr" class="text-green-600 hover:underline">privacy@onlyshoes.fr</a>
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Vous pouvez également déposer une réclamation auprès de la CNIL (Commission Nationale de l'Informatique et des Libertés) : <a href="https://www.cnil.fr" target="_blank" class="text-green-600 hover:underline">www.cnil.fr</a>
                    </p>
                </section>
            </div>
        </div>
    </main>

    <?= view('components/footer') ?>
</body>
</html>
