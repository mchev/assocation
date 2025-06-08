<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

const page = usePage();
const organization = computed(() => page.props.auth.user.current_organization);

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
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
                <Card class="p-6">
                    <nav class="space-y-1">
                        <Button
                            variant="ghost"
                            class="w-full justify-start"
                            :class="{ 'bg-accent': route().current('organizations.settings') }"
                            @click="$inertia.visit(route('organizations.settings'))"
                        >
                            Informations générales
                        </Button>
                        <Button
                            variant="ghost"
                            class="w-full justify-start"
                            :class="{ 'bg-accent': route().current('organizations.settings.members') }"
                            @click="$inertia.visit(route('organizations.settings.members'))"
                        >
                            Membres
                        </Button>
                        <Button
                            variant="ghost"
                            class="w-full justify-start"
                            :class="{ 'bg-accent': route().current('organizations.settings.roles') }"
                            @click="$inertia.visit(route('organizations.settings.roles'))"
                        >
                            Rôles et permissions
                        </Button>
                        <Button
                            variant="ghost"
                            class="w-full justify-start text-red-500 hover:text-red-500 dark:text-red-400"
                            :class="{ 'bg-accent': route().current('organizations.settings.delete') }"
                            @click="$inertia.visit(route('organizations.settings.delete'))"
                        >
                            Supprimer l'organisation
                        </Button>
                    </nav>
                </Card>

                <!-- Content Area -->
                <Card class="col-span-3 p-6">
                    <slot />
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 