<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';

const props = defineProps({
    organization: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.organization.name,
    description: props.organization.description,
    email: props.organization.email,
    phone: props.organization.phone,
    address: props.organization.address,
    city: props.organization.city,
    postal_code: props.organization.postal_code,
    country: props.organization.country || 'FR',
    website: props.organization.website,
});

const updateOrganizationInformation = () => {
    form.patch(route('app.organizations.settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section>
        <HeadingSmall
            title="Informations générales"
            description="Mettez à jour les informations de votre organisation."
        />

        <form @submit.prevent="updateOrganizationInformation" class="mt-6 space-y-6">
            <div class="grid gap-6 sm:grid-cols-2">
                <!-- Nom -->
                <div>
                    <Label for="name">Nom</Label>
                    <Input
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email -->
                <div>
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Téléphone -->
                <div>
                    <Label for="phone">Téléphone</Label>
                    <Input
                        id="phone"
                        type="tel"
                        class="mt-1 block w-full"
                        v-model="form.phone"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <!-- Site web -->
                <div>
                    <Label for="website">Site web</Label>
                    <Input
                        id="website"
                        type="url"
                        class="mt-1 block w-full"
                        v-model="form.website"
                    />
                    <InputError class="mt-2" :message="form.errors.website" />
                </div>

                <!-- Adresse -->
                <div class="sm:col-span-2">
                    <Label for="address">Adresse</Label>
                    <Input
                        id="address"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.address"
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <!-- Ville -->
                <div>
                    <Label for="city">Ville</Label>
                    <Input
                        id="city"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.city"
                    />
                    <InputError class="mt-2" :message="form.errors.city" />
                </div>

                <!-- Code postal -->
                <div>
                    <Label for="postal_code">Code postal</Label>
                    <Input
                        id="postal_code"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.postal_code"
                    />
                    <InputError class="mt-2" :message="form.errors.postal_code" />
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <Label for="description">Description</Label>
                    <Textarea
                        id="description"
                        class="mt-1 block w-full"
                        v-model="form.description"
                        rows="4"
                    />
                    <InputError class="mt-2" :message="form.errors.description" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Sauvegarder</Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Sauvegardé.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template> 