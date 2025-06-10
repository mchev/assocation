<template>
    <AppLayout title="Mentions légales" :description="`Mentions légales de ${name}`">
        <div class="max-w-7xl mx-auto px-4 py-8 prose prose-slate">
            <h1 class="text-2xl font-bold mb-6">Mentions légales</h1>
            
            <p class="mb-4">
                Dernière mise à jour : 10 juin 2025
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">1. Éditeur du site</h2>
            <p class="mb-4">
                Ce site web est édité à titre personnel par Martin Chevignard<br>
                Adresse email : martin@pegase.io
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">2. Hébergement</h2>
            <p class="mb-4">
                Ce site est hébergé par :<br>
                Hetzner Online GmbH<br>
                Industriestr. 25<br>
                91710 Gunzenhausen<br>
                Allemagne<br>
                <br>
                Le nom de domaine est géré par :<br>
                OVH SAS<br>
                2 rue Kellermann<br>
                59100 Roubaix<br>
                France
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">3. Propriété intellectuelle</h2>
            <p class="mb-4">
                L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">4. Liens hypertextes</h2>
            <p class="mb-4">
                Le site peut contenir des liens hypertextes vers d'autres sites internet. La responsabilité de l'éditeur du site ne saurait être engagée au titre d'un site tiers auquel l'utilisateur aurait eu accès via le site.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">5. Protection des données personnelles</h2>
            <p class="mb-4">
                Conformément à la loi Informatique et Libertés du 6 janvier 1978 modifiée et au Règlement Général sur la Protection des Données (RGPD), vous disposez de droits concernant vos données personnelles. Pour plus d'informations, veuillez consulter notre <Link href="/confidentialite" class="text-primary-600 hover:text-primary-800">politique de confidentialité</Link>.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">6. Cookies</h2>
            <p class="mb-4">
                Le site utilise des cookies techniques nécessaires à son bon fonctionnement. Ces cookies ne collectent aucune donnée personnelle.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">7. Droit applicable</h2>
            <p class="mb-4">
                Les présentes mentions légales sont régies par le droit français. En cas de litige, les tribunaux français seront seuls compétents.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">8. Crédits</h2>
            <p class="mb-4">
                Site développé avec Laravel, Vue.js et Tailwind CSS.<br>
                Icons by Lucide.<br>
                Components by shadcn.
            </p>

            <h2 class="text-xl font-semibold mt-6 mb-3">9. Contact</h2>
            <p class="mb-4">
                Pour toute question concernant le site, vous pouvez nous contacter par email à l'adresse suivante : martin@pegase.io
            </p>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
const name = usePage().props.name;
</script>
