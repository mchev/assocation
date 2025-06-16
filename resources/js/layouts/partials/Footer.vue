<script setup>
    import { usePage } from '@inertiajs/vue3';
    const name = usePage().props.name;
</script>

<template>
    <footer class="bg-gray-50 dark:bg-gray-900 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ new Date().getFullYear() }} {{ name }}. Tous droits réservés.
            </p>
            <div class="flex flex-wrap justify-center gap-4 mt-4">
                <a href="/decouvrir" class="text-sm text-gray-500 dark:text-gray-400">Découvrir</a>
                <a href="/comment-ca-marche" class="text-sm text-gray-500 dark:text-gray-400">Comment ça marche</a>
                <a href="/mentions-legales" class="text-sm text-gray-500 dark:text-gray-400">Mentions légales</a>
                <a href="/politique-de-confidentialite" class="text-sm text-gray-500 dark:text-gray-400">Politique de confidentialité</a>
                <a href="/conditions-generales-d-utilisation" class="text-sm text-gray-500 dark:text-gray-400">Conditions générales d'utilisation</a>
            </div>
        </div>
    </footer>
</template>