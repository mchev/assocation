<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    error?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AppLayout title="Connexion à votre compte" description="Entrez votre email et mot de passe ci-dessous pour vous connecter">
        <Head title="Connexion" />
        <template #header>
            <h1 class="text-2xl font-bold">Connexion</h1>
        </template>

        <div class="max-w-xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <div v-if="error" class="mb-4 text-center text-sm font-medium text-red-600">
                {{ error }}
            </div>

            <div class="space-y-6">
                <!-- Social Login -->
                <div class="grid gap-4">
                    <Button variant="outline" class="w-full" asChild>
                        <a href="/auth/google/redirect" rel="nofollow">
                            <img src="https://www.google.com/favicon.ico" alt="Google" class="size-5 mr-2" />
                            Continuer avec Google
                        </a>
                    </Button>
                    <!-- <div class="relative">
                        <Button variant="outline" class="w-full" asChild disabled>
                            <a href="#" rel="nofollow">
                                <img src="https://www.helloasso.com/favicon.ico" alt="HelloAsso" class="size-4 mr-2" />
                                Continuer avec HelloAsso
                            </a>
                        </Button>
                        <div class="absolute -top-1 -right-1 bg-gray-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                            Prochainement
                        </div>
                    </div> -->
                </div>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <Separator class="w-full" />
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-background px-2 text-muted-foreground">Ou</span>
                    </div>
                </div>

                <form @submit.prevent="submit" class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="email">Adresse email</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@exemple.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password">Mot de passe</Label>
                            <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                                Mot de passe oublié ?
                            </TextLink>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Mot de passe"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <Label for="remember" class="flex items-center space-x-3">
                            <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                            <span>Se souvenir de moi</span>
                        </Label>
                    </div>

                    <Button type="submit" class="w-full" :tabindex="4" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        Se connecter
                    </Button>
                </form>

                <div class="text-center text-sm text-muted-foreground">
                    Vous n'avez pas de compte ?
                    <TextLink :href="route('register')" :tabindex="5">S'inscrire</TextLink>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
