<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import OrganisationLayout from '@/layouts/settings/OrganisationLayout.vue';
import GeneralForm from './Partials/GeneralForm.vue';

defineProps({
    organization: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <Head title="Paramètres de l'organisation" />

        <OrganisationLayout>
            <GeneralForm :organization="organization"/>
        </OrganisationLayout>
    </AppLayout>
</template> 