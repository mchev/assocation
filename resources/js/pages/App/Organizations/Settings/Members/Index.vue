<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import OrganisationLayout from '@/layouts/settings/OrganisationLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { ref } from 'vue';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

defineProps({
    organization: {
        type: Object,
        required: true
    },
    members: {
        type: Array,
        required: true
    },
    pendingInvitations: {
        type: Array,
        required: true
    },
    roles: {
        type: Array,
        required: true
    }
});

const isModalOpen = ref(false);

const form = useForm({
    email: '',
    role: '',
});

const formatDate = (date) => {
    return format(new Date(date), 'PPP', { locale: fr });
};

const inviteMember = () => {
    form.post(route('app.organizations.members.invite'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            isModalOpen.value = false;
        },
    });
};

const removeMember = (member) => {
    if (!confirm(`Êtes-vous sûr de vouloir retirer ${member.name} de l'organisation ?`)) {
        return;
    }

    router.delete(route('app.organizations.members.remove', member.id), {
        preserveScroll: true
    });
};

const cancelInvitation = (invitation) => {
    if (!confirm(`Êtes-vous sûr de vouloir annuler l'invitation de ${invitation.email} ?`)) {
        return;
    }

    router.delete(route('app.organizations.invitations.cancel', invitation.id), {
        preserveScroll: true
    });
};
</script>

<template>
    <AppLayout :title="'Gestion des membres'">
        <OrganisationLayout>
    <section class="space-y-6">
        <HeadingSmall
            title="Gestion des membres"
            description="Invitez et gérez les membres de votre organisation."
        />

        <div class="space-y-4">
            <Dialog v-model:open="isModalOpen">
                <DialogTrigger asChild>
                    <Button>Inviter un membre</Button>
                </DialogTrigger>
                <DialogContent>
                    <form @submit.prevent="inviteMember">
                        <DialogHeader>
                            <DialogTitle>Inviter un nouveau membre</DialogTitle>
                            <DialogDescription>
                                Envoyez une invitation par email pour rejoindre votre organisation.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-4 py-4">
                            <div class="grid gap-2">
                                <Label for="email">Adresse email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    placeholder="email@example.com"
                                    required
                                />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="role">Rôle</Label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="rounded-md border-0 bg-white py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary dark:bg-gray-950 dark:text-gray-100 dark:ring-gray-800 sm:text-sm sm:leading-6"
                                    required
                                >
                                    <option value="">Sélectionnez un rôle</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.role" />
                            </div>
                        </div>

                        <DialogFooter>
                            <Button type="submit" :disabled="form.processing">
                                Envoyer l'invitation
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Active Members -->
            <div class="rounded-md border">
                <div class="px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                        Membres actifs
                    </h3>
                </div>
                <div class="border-t">
                    <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-800">
                        <li v-for="member in members" :key="member.id" class="flex items-center justify-between gap-x-6 px-4 py-5 sm:px-6">
                            <div class="min-w-0">
                                <div class="flex items-start gap-x-3">
                                    <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100">
                                        {{ member.name }}
                                    </p>
                                    <p class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                        :class="member.role === 'admin' ? 'text-green-700 bg-green-50 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' : 'text-gray-600 bg-gray-50 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20'"
                                    >
                                        {{ member.role === 'admin' ? 'Administrateur' : 'Membre' }}
                                    </p>
                                </div>
                                <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                    <p class="whitespace-nowrap">{{ member.email }}</p>
                                </div>
                            </div>
                            <div class="flex flex-none items-center gap-x-4">
                                <Button
                                    v-if="member.id !== organization.owner_id"
                                    variant="ghost"
                                    @click="removeMember(member)"
                                >
                                    Retirer
                                </Button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Pending Invitations -->
            <div v-if="pendingInvitations.length > 0" class="rounded-md border">
                <div class="px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                        Invitations en attente
                    </h3>
                </div>
                <div class="border-t">
                    <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-800">
                        <li v-for="invitation in pendingInvitations" :key="invitation.id" class="flex items-center justify-between gap-x-6 px-4 py-5 sm:px-6">
                            <div class="min-w-0">
                                <div class="flex items-start gap-x-3">
                                    <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100">
                                        {{ invitation.email }}
                                    </p>
                                    <p class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                        :class="invitation.role === 'admin' ? 'text-green-700 bg-green-50 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' : 'text-gray-600 bg-gray-50 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20'"
                                    >
                                        {{ invitation.role === 'admin' ? 'Administrateur' : 'Membre' }}
                                    </p>
                                </div>
                                <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                    <p class="whitespace-nowrap">Invité le {{ formatDate(invitation.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex flex-none items-center gap-x-4">
                                <Button
                                    variant="ghost"
                                    @click="cancelInvitation(invitation)"
                                >
                                    Annuler
                                </Button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            </div>
        </section>
    </OrganisationLayout>
</AppLayout>
</template> 