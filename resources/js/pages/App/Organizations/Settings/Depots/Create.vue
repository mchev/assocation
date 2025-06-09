<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import OrganisationLayout from '@/layouts/settings/OrganisationLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AddressInput from '@/components/AddressInput.vue';
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';

const location = ref(null);

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

watch(location, (value) => {
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
        onSuccess: () => {
            router.visit(route('app.organizations.depots.index'));
        },
    });
};
</script>

<template>
    <AppLayout :title="'Créer un dépôt'">
        <OrganisationLayout>
    <section class="space-y-6">
        <HeadingSmall
            title="Créer un dépôt"
            description="Ajoutez un nouveau lieu de stockage à votre organisation."
        />

        <div class="max-w-2xl">
            <form @submit.prevent="createDepot" class="space-y-8">
                <!-- Informations générales -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b">
                        <span class="text-lg font-semibold">Informations générales</span>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Nom du dépôt</Label>
                        <p class="text-sm text-muted-foreground">
                            Évitez d'inclure des informations précises de localisation dans le nom car il sera affiché publiquement avec la ville.
                        </p>
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="Ex: Dépôt principal, Entrepôt sud..."
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <p class="text-sm text-muted-foreground">
                            Décrivez brièvement ce dépôt et ses caractéristiques principales.
                        </p>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Ex: Grand entrepôt avec accès poids lourds..."
                            rows="3"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                </div>

                <!-- Localisation -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b">
                        <span class="text-lg font-semibold">Localisation</span>
                    </div>

                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="location">Adresse</Label>
                            <p class="text-sm text-muted-foreground">
                                Une adresse précise nous permet de géolocaliser votre dépôt et d'améliorer les résultats de recherche d'équipements en fonction de la distance. Pour des raisons de sécurité, seule la ville sera visible publiquement sur le site.
                            </p>
                            <AddressInput
                                id="location"
                                v-model="location"
                                :placeholder="'Rechercher une adresse'"
                                required
                            />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="grid gap-4 p-4 bg-muted/50 rounded-lg border">
                            <div class="grid gap-2">
                                <Label for="address">Adresse complète</Label>
                                <Input
                                    id="address"
                                    v-model="form.address"
                                    readonly
                                    disabled
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="city">Ville</Label>
                                    <Input
                                        id="city"
                                        v-model="form.city"
                                        readonly
                                        disabled
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="postal_code">Code postal</Label>
                                    <Input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        readonly
                                        disabled
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-4 border-t">
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit(route('app.organizations.depots.index'))"
                    >
                        Annuler
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        Créer le dépôt
                    </Button>
                </div>
            </form>
        </div>
    </section>
</OrganisationLayout>
</AppLayout>
</template>
