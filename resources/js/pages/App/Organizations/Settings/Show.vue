<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import GeneralForm from './Partials/GeneralForm.vue';
import MembersManagement from './Partials/MembersManagement.vue';
import DeleteOrganization from './Partials/DeleteOrganization.vue';
import DepotsManagement from './Partials/DepotsManagement.vue';

const props = defineProps({
    organization: {
        type: Object,
        required: true,
    },
    members: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
    section: {
        type: String,
        required: true,
    },
    depots: {
        type: Array,
        default: () => [],
    },
});

const sections = [
    {
        name: 'general',
        label: 'Informations générales',
        route: 'app.organizations.settings',
    },
    {
        name: 'members',
        label: 'Membres',
        route: 'app.organizations.settings.members',
    },
    {
        name: 'depots',
        label: 'Dépôts',
        route: 'app.organizations.depots.index',
    },
    {
        name: 'delete',
        label: 'Supprimer l\'organisation',
        route: 'app.organizations.settings.delete',
        class: 'text-red-500 dark:text-red-400 hover:text-red-500',
    },
];
</script>

<template>
    <Head title="Paramètres de l'organisation" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Paramètres de l'organisation
            </h2>
        </template>

        <div class="space-y-6 p-4 sm:p-8">
            <div class="grid gap-6 md:grid-cols-4">
                <!-- Navigation Sidebar -->
                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm dark:border-gray-800">
                    <nav class="space-y-1">
                        <a
                            v-for="item in sections"
                            :key="item.name"
                            :href="route(item.route)"
                            class="flex w-full items-center justify-start rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                            :class="[
                                { 'bg-accent': section === item.name },
                                item.class
                            ]"
                        >
                            {{ item.label }}
                        </a>
                    </nav>
                </div>

                <!-- Content Area -->
                <div class="col-span-3 rounded-lg border bg-card p-6 text-card-foreground shadow-sm dark:border-gray-800">
                    <GeneralForm
                        v-if="section === 'general'"
                        :organization="organization"
                    />

                    <MembersManagement
                        v-if="section === 'members'"
                        :members="members"
                        :roles="roles"
                    />

                    <DepotsManagement
                        v-if="section === 'depots'"
                        :depots="depots"
                    />

                    <DeleteOrganization
                        v-if="section === 'delete'"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template> 