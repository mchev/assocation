<script setup>
import { watch, ref, nextTick } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AddressInput from '@/components/AddressInput.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps({
    depots: {
        type: Array,
        default: () => [],
    },
});

const createDialogOpen = ref(false);
const editDialogOpen = ref(false);
const isInitializing = ref(false);

const form = useForm({
    name: '',
    description: '',
    address: '',
    city: '',
    latitude: '',
    longitude: '',
    postal_code: '',
    country: 'FR',
});

const location = ref(null);

const resetForm = () => {
    form.reset();
    form.clearErrors();
    location.value = null;
};

watch(location, (value) => {
    if (isInitializing.value) return;
    
    if (!value) {
        form.address = '';
        form.city = '';
        form.postal_code = '';
        form.latitude = '';
        form.longitude = '';
        return;
    }

    // Construct full address from housenumber and street
    const addressParts = [];
    if (value.housenumber) addressParts.push(value.housenumber);
    if (value.street) addressParts.push(value.street);
    
    form.address = addressParts.join(' ');
    form.city = value.city;
    form.postal_code = value.postcode;
    form.latitude = value.lat;
    form.longitude = value.lng;
});

const createDepot = () => {
    form.post(route('app.organizations.depots.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
            createDialogOpen.value = false;
        },
    });
};

const initEditForm = (depot) => {
    isInitializing.value = true;
    
    // First set the form data
    form.name = depot.name;
    form.description = depot.description;
    form.address = depot.address;
    form.city = depot.city;
    form.latitude = depot.latitude;
    form.longitude = depot.longitude;
    form.postal_code = depot.postal_code;
    form.country = depot.country;
    
    // Then set the location in the next tick to avoid reactivity issues
    nextTick(() => {
        location.value = {
            name: depot.address,
            street: depot.address,
            city: depot.city,
            postcode: depot.postal_code,
            lat: depot.latitude,
            lng: depot.longitude
        };
        
        // Reset the initialization flag after everything is set
        isInitializing.value = false;
    });
};

const editDepot = (depot) => {
    form.put(route('app.organizations.depots.update', depot.id), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
            editDialogOpen.value = false;
        },
    });
};

const deleteDepot = (depot) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce dépôt ?')) {
        router.delete(route('app.organizations.depots.destroy', depot.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <section class="space-y-6">
        <HeadingSmall
            title="Gestion des dépôts"
            description="Gérez les lieux de stockage de votre organisation."
        />

        <div class="space-y-4">
            <Dialog v-model:open="createDialogOpen">
                <DialogTrigger asChild>
                    <Button @click="resetForm">Ajouter un dépôt</Button>
                </DialogTrigger>
                <DialogContent>
                    <form @submit.prevent="createDepot">
                        <DialogHeader>
                            <DialogTitle>Ajouter un nouveau dépôt</DialogTitle>
                            <DialogDescription>
                                Créez un nouveau lieu de stockage pour votre organisation.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-4 py-4">
                            <div class="grid gap-2">
                                <Label for="name">Nom du dépôt</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    required
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="description">Description</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="address">Adresse</Label>
                                <AddressInput
                                    id="location"
                                    v-model="location"
                                    :placeholder="'Rechercher une adresse'"
                                    required
                                />
                                <InputError :message="form.errors.address" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="city">Ville</Label>
                                    <Input
                                        id="city"
                                        v-model="form.city"
                                        required
                                    />
                                    <InputError :message="form.errors.city" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="postal_code">Code postal</Label>
                                    <Input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        required
                                    />
                                    <InputError :message="form.errors.postal_code" />
                                </div>
                            </div>
                        </div>

                        <DialogFooter>
                            <Button type="submit" :disabled="form.processing">
                                Créer le dépôt
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <div class="rounded-md border dark:border-gray-800">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nom</TableHead>
                            <TableHead>Adresse</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="depot in depots" :key="depot.id">
                            <TableCell>
                                <div class="font-medium">{{ depot.name }}</div>
                                <div class="text-sm text-gray-500">{{ depot.description }}</div>
                            </TableCell>
                            <TableCell>
                                <div>{{ depot.address }}</div>
                                <div>{{ depot.postal_code }} {{ depot.city }}</div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Dialog v-model:open="editDialogOpen">
                                        <DialogTrigger asChild>
                                            <Button 
                                                variant="outline" 
                                                size="sm" 
                                                @click.stop="() => {
                                                    editDialogOpen = true;
                                                    initEditForm(depot);
                                                }"
                                            >
                                                Modifier
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <form @submit.prevent="editDepot(depot)">
                                                <DialogHeader>
                                                    <DialogTitle>Modifier le dépôt</DialogTitle>
                                                </DialogHeader>

                                                <div class="grid gap-4 py-4">
                                                    <div class="grid gap-2">
                                                        <Label for="edit-name">Nom du dépôt</Label>
                                                        <Input
                                                            id="edit-name"
                                                            v-model="form.name"
                                                            required
                                                        />
                                                        <InputError :message="form.errors.name" />
                                                    </div>

                                                    <div class="grid gap-2">
                                                        <Label for="edit-description">Description</Label>
                                                        <Textarea
                                                            id="edit-description"
                                                            v-model="form.description"
                                                            rows="3"
                                                        />
                                                        <InputError :message="form.errors.description" />
                                                    </div>

                                                    <div class="grid gap-2">
                                                        <Label for="edit-address">Adresse</Label>
                                                        <AddressInput
                                                            id="edit-location"
                                                            v-model="location"
                                                            :placeholder="'Rechercher une adresse'"
                                                            required
                                                        />
                                                        <InputError :message="form.errors.address" />
                                                    </div>
                                                </div>

                                                <DialogFooter>
                                                    <Button type="submit" :disabled="form.processing">
                                                        Sauvegarder
                                                    </Button>
                                                </DialogFooter>
                                            </form>
                                        </DialogContent>
                                    </Dialog>

                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteDepot(depot)"
                                    >
                                        Supprimer
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </section>
</template> 