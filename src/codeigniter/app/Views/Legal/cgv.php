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
            <h1 class="text-4xl font-bold text-gray-900 mb-6">Conditions Générales de Vente</h1>
            <p class="text-sm text-gray-500 mb-8">Dernière mise à jour : <?= date('d/m/Y') ?></p>

            <div class="prose prose-lg max-w-none">
                <!-- Article 1 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 1 - Objet</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Les présentes Conditions Générales de Vente (CGV) régissent les relations contractuelles entre OnlyShoes (ci-après "la Plateforme") et les utilisateurs (ci-après "Vendeurs" et "Acheteurs") dans le cadre de l'achat et de la vente de chaussures d'occasion ou neuves via la plateforme OnlyShoes.
                    </p>
                </section>

                <!-- Article 2 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 2 - Acceptation des CGV</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        L'utilisation de la Plateforme implique l'acceptation pleine et entière des présentes CGV. En créant un compte ou en effectuant une transaction, vous reconnaissez avoir lu et accepté ces conditions.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Toute modification des CGV sera communiquée aux utilisateurs et prendra effet immédiatement après publication sur le site.
                    </p>
                </section>

                <!-- Article 3 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 3 - Création de compte</h2>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">3.1 Inscription</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Pour utiliser la Plateforme, vous devez créer un compte en fournissant des informations exactes et à jour (nom, prénom, email). Vous êtes responsable de la confidentialité de votre mot de passe.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">3.2 Conditions d'accès</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Être âgé d'au moins 18 ans ou disposer de l'autorisation parentale</li>
                        <li>Fournir des informations exactes et véridiques</li>
                        <li>Ne créer qu'un seul compte par personne</li>
                        <li>Respecter les lois en vigueur et les présentes CGV</li>
                    </ul>
                </section>

                <!-- Article 4 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 4 - Publication d'annonces</h2>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">4.1 Obligations du Vendeur</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">Le Vendeur s'engage à :</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Décrire les produits de manière exacte et détaillée</li>
                        <li>Publier des photos authentiques du produit vendu</li>
                        <li>Indiquer l'état réel du produit (neuf, comme neuf, très bon, bon, correct)</li>
                        <li>Fixer un prix raisonnable et conforme au marché</li>
                        <li>Être propriétaire légitime du produit</li>
                        <li>Ne vendre que des produits authentiques (pas de contrefaçons)</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">4.2 Produits interdits</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">Il est strictement interdit de vendre :</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Des contrefaçons ou produits de marque non authentiques</li>
                        <li>Des produits volés</li>
                        <li>Des produits dangereux ou non conformes aux normes</li>
                        <li>Des produits dont la vente est interdite par la loi</li>
                    </ul>
                </section>

                <!-- Article 5 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 5 - Achat et paiement</h2>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">5.1 Commission de la Plateforme</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        OnlyShoes prélève une <strong>commission de 12%</strong> sur chaque vente réalisée via la Plateforme. Cette commission couvre :
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Les frais de fonctionnement et maintenance de la Plateforme</li>
                        <li>Le traitement sécurisé des paiements</li>
                        <li>Le support client et la médiation en cas de litige</li>
                        <li>Les services de recherche sémantique et recommandation</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        <strong>Exemple :</strong> Pour une vente de 100€, le Vendeur recevra 88€ (100€ - 12€ de commission).
                    </p>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">5.2 Processus d'achat</h3>
                    <ol class="list-decimal list-inside text-gray-700 mb-4 space-y-2">
                        <li>L'Acheteur sélectionne un produit et clique sur "Acheter"</li>
                        <li>L'Acheteur choisit son mode de paiement</li>
                        <li>Le paiement est traité de manière sécurisée</li>
                        <li>Une fois validé, la commande est créée et la commission est prélevée</li>
                    </ol>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">5.3 Moyens de paiement acceptés</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Carte bancaire (Visa, Mastercard, American Express)</li>
                        <li>PayPal</li>
                        <li>Apple Pay / Google Pay</li>
                        <li>Cryptomonnaies (Bitcoin, Ethereum, Monero)</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">5.3 Sécurisation des paiements</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Tous les paiements sont sécurisés via nos prestataires certifiés PCI-DSS. OnlyShoes ne conserve jamais les coordonnées bancaires complètes.
                    </p>
                </section>

                <!-- Article 6 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 6 - Livraison</h2>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">6.1 Responsabilité du Vendeur</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le Vendeur s'engage à expédier le produit dans un délai de <strong>3 jours ouvrés</strong> suivant la validation du paiement. L'emballage doit être soigné pour éviter tout dommage pendant le transport.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">6.2 Suivi de commande</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le Vendeur doit mettre à jour le statut de la commande :
                    </p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li><strong>En préparation :</strong> produit en cours d'emballage</li>
                        <li><strong>Expédiée :</strong> produit envoyé avec numéro de suivi</li>
                        <li><strong>Livrée :</strong> produit reçu par l'Acheteur</li>
                    </ul>
                </section>

                <!-- Article 7 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 7 - Droit de rétractation</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        <strong>Important :</strong> Conformément à l'article L221-28 du Code de la consommation, le droit de rétractation ne s'applique pas aux ventes entre particuliers.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Si le Vendeur est un professionnel, l'Acheteur dispose d'un délai de <strong>14 jours</strong> à compter de la réception du produit pour exercer son droit de rétractation, sans justification ni pénalité.
                    </p>
                </section>

                <!-- Article 8 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 8 - Réclamations et litiges</h2>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">8.1 Non-conformité</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Si le produit reçu ne correspond pas à la description ou présente un défaut non mentionné, l'Acheteur doit contacter le Vendeur dans un délai de <strong>48 heures</strong> suivant la réception.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">8.2 Résolution amiable</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        En cas de litige, les parties s'engagent à rechercher une solution amiable. OnlyShoes peut jouer un rôle de médiateur si nécessaire.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">8.3 Système d'avis</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Après une transaction, l'Acheteur peut laisser un avis sur le Vendeur (note de 1 à 5 étoiles et commentaire). Les avis frauduleux ou abusifs seront supprimés.
                    </p>
                </section>

                <!-- Article 9 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 9 - Signalements et sanctions</h2>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">9.1 Système de signalement</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Tout utilisateur peut signaler :
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Une annonce frauduleuse ou contrefaite</li>
                        <li>Un avis abusif</li>
                        <li>Un comportement inapproprié d'un utilisateur</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3">9.2 Sanctions</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        En cas de manquement aux CGV, OnlyShoes se réserve le droit de :
                    </p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Supprimer une annonce</li>
                        <li>Suspendre temporairement un compte</li>
                        <li>Bannir définitivement un utilisateur</li>
                        <li>Poursuivre en justice en cas de fraude avérée</li>
                    </ul>
                </section>

                <!-- Article 10 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 10 - Responsabilité de la Plateforme</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        OnlyShoes est une plateforme de mise en relation entre Vendeurs et Acheteurs. Elle n'est pas partie aux transactions et ne peut être tenue responsable :
                    </p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>De la qualité, l'authenticité ou la conformité des produits vendus</li>
                        <li>Des litiges entre Vendeurs et Acheteurs</li>
                        <li>Des retards ou pertes de colis imputables aux transporteurs</li>
                        <li>De l'utilisation frauduleuse de la Plateforme par des tiers</li>
                    </ul>
                </section>

                <!-- Article 11 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 11 - Propriété intellectuelle</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Tous les éléments de la Plateforme (logo, design, textes, code) sont protégés par le droit d'auteur. Toute reproduction, même partielle, est interdite sans autorisation écrite préalable.
                    </p>
                </section>

                <!-- Article 12 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 12 - Protection des données personnelles</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Le traitement de vos données personnelles est détaillé dans notre <a href="/confidentialite" class="text-green-600 hover:underline">Politique de confidentialité</a>, conforme au RGPD.
                    </p>
                </section>

                <!-- Article 13 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 13 - Loi applicable et juridiction</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Les présentes CGV sont régies par le droit français. En cas de litige, et après échec d'une tentative de résolution amiable, les tribunaux français seront seuls compétents.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Conformément à la législation européenne, l'Acheteur peut également recourir à la plateforme de règlement en ligne des litiges (RLL) de la Commission européenne : <a href="https://ec.europa.eu/consumers/odr" target="_blank" class="text-green-600 hover:underline">ec.europa.eu/consumers/odr</a>
                    </p>
                </section>

                <!-- Article 14 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 14 - Contact</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Pour toute question concernant les présentes CGV :<br>
                        <strong>Email :</strong> <a href="mailto:support@onlyshoes.fr" class="text-green-600 hover:underline">support@onlyshoes.fr</a>
                    </p>
                </section>
            </div>
        </div>
    </main>

    <?= view('components/footer') ?>
</body>
</html>
