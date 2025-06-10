<template>
    <AppLayout title="Politique de confidentialité" :description="`Politique de confidentialité de ${name}`">
        <div class="max-w-7xl mx-auto px-4 py-8 prose prose-slate">
            <h1 class="text-2xl font-bold mb-6">Politique de confidentialité</h1>
            
            <p class="mb-4">
                Dernière mise à jour : 10 juin 2025
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">1. Introduction</h2>
            <p class="mb-4">
                Cette politique de confidentialité explique comment {{ name }} ("nous", "notre", "nos") collecte, utilise et protège vos données personnelles lorsque vous utilisez notre site web. Nous nous engageons à assurer la protection de votre vie privée et de vos données personnelles conformément au Règlement Général sur la Protection des Données (RGPD) et aux lois françaises applicables.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">2. Collecte des données</h2>
            <p class="mb-4">
                Nous collectons uniquement les données nécessaires au fonctionnement du site :
            </p>
            <ul class="list-disc ml-6 mb-4">
                <li>Données de connexion (adresse IP, date et heure de connexion)</li>
                <li>Cookies techniques essentiels au fonctionnement du site</li>
            </ul>

            <h2 class="text-xl font-semibold mt-6 mb-3">3. Utilisation des données</h2>
            <p class="mb-4">
                Les données collectées sont utilisées exclusivement pour :
            </p>
            <ul class="list-disc ml-6 mb-4">
                <li>Assurer le bon fonctionnement technique du site</li>
                <li>Garantir la sécurité du site</li>
                <li>Améliorer l'expérience utilisateur</li>
            </ul>

            <h2 class="text-xl font-semibold mt-6 mb-3">4. Conservation des données</h2>
            <p class="mb-4">
                Les données sont conservées pendant une durée limitée strictement nécessaire aux finalités pour lesquelles elles sont collectées. Les logs de connexion sont automatiquement supprimés après 12 mois.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">5. Partage des données</h2>
            <p class="mb-4">
                Nous ne partageons, ne vendons, ni ne transmettons vos données personnelles à des tiers. Aucune donnée n'est utilisée à des fins commerciales ou marketing.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">6. Vos droits</h2>
            <p class="mb-4">
                Conformément au RGPD, vous disposez des droits suivants concernant vos données personnelles :
            </p>
            <ul class="list-disc ml-6 mb-4">
                <li>Droit d'accès</li>
                <li>Droit de rectification</li>
                <li>Droit à l'effacement</li>
                <li>Droit à la limitation du traitement</li>
                <li>Droit à la portabilité des données</li>
                <li>Droit d'opposition</li>
            </ul>

            <h2 class="text-xl font-semibold mt-6 mb-3">7. Cookies</h2>
            <p class="mb-4">
                Notre site utilise uniquement des cookies techniques essentiels au fonctionnement du site. Ces cookies ne collectent aucune donnée personnelle et sont automatiquement supprimés à la fermeture de votre navigateur.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">8. Sécurité</h2>
            <p class="mb-4">
                Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données contre tout accès, modification, divulgation ou destruction non autorisés.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">9. Contact</h2>
            <p class="mb-4">
                Pour toute question concernant cette politique de confidentialité ou pour exercer vos droits, vous pouvez nous contacter par email.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">10. Modifications</h2>
            <p class="mb-4">
                Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. Les modifications prendront effet dès leur publication sur le site.
            </p>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/layouts/AppLayout.vue';
    import { usePage } from '@inertiajs/vue3';
    const name = usePage().props.name;
</script>