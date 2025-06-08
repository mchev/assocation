<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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

const form = useForm({
    name: '',
    description: '',
    address: '',
    city: '',
    postal_code: '',
    country: 'FR',
});

const createDepot = () => {
    form.post(route('app.organizations.depots.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const editDepot = (depot) => {
    form.name = depot.name;
    form.description = depot.description;
    form.address = depot.address;
    form.city = depot.city;
    form.postal_code = depot.postal_code;
    form.country = depot.country;
    
    form.put(route('app.organizations.depots.update', depot.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
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
            <Dialog>
                <DialogTrigger asChild>
                    <Button>Ajouter un dépôt</Button>
                </DialogTrigger>
                <DialogContent>
                    <form @submit.prevent="createDepot">
                        <DialogHeader>
                            <DialogTitle>Ajouter un nouveau dépôt</DialogTitle>
                            <DialogDescription>
                                Créez un nouveau dépôt pour votre organisation.
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
                                <Input
                                    id="address"
                                    v-model="form.address"
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
                                    <Dialog>
                                        <DialogTrigger asChild>
                                            <Button variant="outline" size="sm">
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
                                                        <Input
                                                            id="edit-address"
                                                            v-model="form.address"
                                                            required
                                                        />
                                                        <InputError :message="form.errors.address" />
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="grid gap-2">
                                                            <Label for="edit-city">Ville</Label>
                                                            <Input
                                                                id="edit-city"
                                                                v-model="form.city"
                                                                required
                                                            />
                                                            <InputError :message="form.errors.city" />
                                                        </div>

                                                        <div class="grid gap-2">
                                                            <Label for="edit-postal_code">Code postal</Label>
                                                            <Input
                                                                id="edit-postal_code"
                                                                v-model="form.postal_code"
                                                                required
                                                            />
                                                            <InputError :message="form.errors.postal_code" />
                                                        </div>
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