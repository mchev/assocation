<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import OrganisationLayout from '@/layouts/settings/OrganisationLayout.vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { MapPin, Building2, Pencil, Trash2, AlertTriangle } from 'lucide-vue-next';
import HeadingSmall from '@/components/HeadingSmall.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ref } from 'vue';

defineProps({
    depots: {
        type: Array,
        default: () => [],
    },
});

const showDeleteDialog = ref(false);
const selectedDepot = ref(null);

const openDeleteDialog = (depot) => {
    selectedDepot.value = depot;
    showDeleteDialog.value = true;
};

const deleteDepot = () => {
    router.delete(route('app.organizations.depots.destroy', selectedDepot.value.id), {
        preserveScroll: true,
    });
    showDeleteDialog.value = false;
};
</script>

<template>
    <AppLayout :title="'Gestion des dépôts'">
        <OrganisationLayout>
            <section class="space-y-6">
                <HeadingSmall
                    title="Gestion des dépôts"
                    description="Gérez les lieux de stockage de votre organisation."
                />

                <div class="space-y-4">
                    <div class="flex justify-end">
                        <Button @click="router.visit(route('app.organizations.depots.create'))">
                            Ajouter un dépôt
                        </Button>
                    </div>

                    <div class="divide-y divide-gray-200 rounded-lg border">
                        <div v-for="depot in depots" :key="depot.id" class="group p-4 sm:p-6 hover:bg-gray-50">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <Building2 class="h-5 w-5 text-primary-500 flex-shrink-0" />
                                        <h3 class="text-base font-semibold text-gray-900">{{ depot.name }}</h3>
                                    </div>
                                    
                                    <p v-if="depot.description" class="text-sm text-gray-500">
                                        {{ depot.description }}
                                    </p>

                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <MapPin class="h-4 w-4 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <div>{{ depot.address }}</div>
                                            <div>{{ depot.postal_code }} {{ depot.city }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-start sm:self-center">
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="router.visit(route('app.organizations.depots.edit', depot.id))"
                                    >
                                        <Pencil class="h-4 w-4 mr-2" />
                                        Modifier
                                    </Button>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="openDeleteDialog(depot)"
                                    >
                                        <Trash2 class="h-4 w-4 mr-2" />
                                        Supprimer
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </OrganisationLayout>

        <Dialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <AlertTriangle class="h-5 w-5 text-destructive" />
                        Confirmation de suppression
                    </DialogTitle>
                    <DialogDescription class="space-y-4">
                        <p>
                            Vous êtes sur le point de supprimer le dépôt
                            <span class="font-medium text-foreground">{{ selectedDepot?.name }}</span>.
                        </p>

                        <div v-if="selectedDepot?.equipments?.length" class="rounded-lg border p-4 space-y-2">
                            <p class="font-medium text-foreground">
                                Ce dépôt contient les équipements suivants qui devront être réaffectés :
                            </p>
                            <ul class="list-disc list-inside space-y-1">
                                <li v-for="equipment in selectedDepot.equipments" :key="equipment.id" class="text-sm">
                                    {{ equipment.name }}
                                </li>
                            </ul>
                        </div>

                        <p v-else class="text-sm">
                            Ce dépôt ne contient aucun équipement.
                        </p>
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="showDeleteDialog = false"
                    >
                        Annuler
                    </Button>
                    <Button
                        variant="destructive"
                        @click="deleteDepot"
                    >
                        Confirmer la suppression
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>