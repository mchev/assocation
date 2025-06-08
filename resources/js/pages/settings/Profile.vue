<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Paramètres du profil',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    address: user.address || '',
    city: user.city || '',
    postal_code: user.postal_code || '',
    country: user.country || 'FR',
    preferred_language: user.preferred_language || 'fr',
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Paramètres du profil" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Informations du profil" description="Mettez à jour vos informations personnelles" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Nom</Label>
                            <Input id="name" v-model="form.name" required autocomplete="name" placeholder="Nom complet" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Adresse e-mail</Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="Adresse e-mail"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">Téléphone</Label>
                            <Input
                                id="phone"
                                type="tel"
                                v-model="form.phone"
                                autocomplete="tel"
                                placeholder="Numéro de téléphone"
                            />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="preferred_language">Langue préférée</Label>
                            <select
                                id="preferred_language"
                                v-model="form.preferred_language"
                                class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <option value="fr">Français</option>
                                <option value="en">English</option>
                            </select>
                            <InputError :message="form.errors.preferred_language" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="address">Adresse</Label>
                        <Input
                            id="address"
                            v-model="form.address"
                            autocomplete="street-address"
                            placeholder="Adresse postale"
                        />
                        <InputError :message="form.errors.address" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <div class="grid gap-2">
                            <Label for="postal_code">Code postal</Label>
                            <Input
                                id="postal_code"
                                v-model="form.postal_code"
                                autocomplete="postal-code"
                                placeholder="Code postal"
                            />
                            <InputError :message="form.errors.postal_code" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="city">Ville</Label>
                            <Input
                                id="city"
                                v-model="form.city"
                                autocomplete="address-level2"
                                placeholder="Ville"
                            />
                            <InputError :message="form.errors.city" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="country">Pays</Label>
                            <Input
                                id="country"
                                v-model="form.country"
                                autocomplete="country"
                                placeholder="Pays"
                            />
                            <InputError :message="form.errors.country" />
                        </div>
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Votre adresse e-mail n'est pas vérifiée.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Cliquez ici pour renvoyer l'e-mail de vérification.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Enregistrer</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Enregistré.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
