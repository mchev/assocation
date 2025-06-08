<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const deleteOrganization = (e) => {
    e.preventDefault();

    form.delete(route('organizations.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            title="Supprimer l'organisation"
            description="Supprimez définitivement votre organisation et toutes ses ressources"
        />
        <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Attention</p>
                <p class="text-sm">Veuillez procéder avec prudence, cette action est irréversible.</p>
            </div>
            <Dialog>
                <DialogTrigger asChild>
                    <Button variant="destructive">Supprimer l'organisation</Button>
                </DialogTrigger>
                <DialogContent>
                    <form class="space-y-6" @submit="deleteOrganization">
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Êtes-vous sûr de vouloir supprimer cette organisation ?</DialogTitle>
                            <DialogDescription>
                                Une fois l'organisation supprimée, toutes ses ressources et données seront définitivement effacées. Veuillez saisir votre
                                mot de passe pour confirmer que vous souhaitez supprimer définitivement l'organisation.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only">Mot de passe</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                ref="passwordInput"
                                v-model="form.password"
                                placeholder="Mot de passe"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose asChild>
                                <Button variant="secondary" @click="closeModal">Annuler</Button>
                            </DialogClose>

                            <Button variant="destructive" :disabled="form.processing">
                                <button type="submit">Supprimer l'organisation</button>
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template> 