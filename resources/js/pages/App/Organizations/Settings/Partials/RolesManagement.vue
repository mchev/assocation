<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
    permissions: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    permissions: [],
});

const createRole = () => {
    form.post(route('organizations.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const deleteRole = (role) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')) {
        router.delete(route('organizations.roles.destroy', role.id), {
            preserveScroll: true,
        });
    }
};

const updateRolePermissions = (role, permission) => {
    router.post(route('organizations.roles.toggle-permission', { role: role.id }), {
        permission: permission.id,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <section class="space-y-6">
        <HeadingSmall
            title="Rôles et permissions"
            description="Gérez les rôles et les permissions de votre organisation."
        />

        <div class="space-y-4">
            <Dialog>
                <DialogTrigger asChild>
                    <Button>Créer un nouveau rôle</Button>
                </DialogTrigger>
                <DialogContent>
                    <form @submit.prevent="createRole">
                        <DialogHeader>
                            <DialogTitle>Créer un nouveau rôle</DialogTitle>
                            <DialogDescription>
                                Définissez un nouveau rôle avec ses permissions associées.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-4 py-4">
                            <div class="grid gap-2">
                                <Label for="name">Nom du rôle</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Ex: Administrateur"
                                    required
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label>Permissions</Label>
                                <div class="grid gap-2">
                                    <div v-for="permission in permissions" :key="permission.id" class="flex items-center space-x-2">
                                        <Checkbox
                                            :id="'permission-' + permission.id"
                                            v-model:checked="form.permissions"
                                            :value="permission.id"
                                        />
                                        <Label :for="'permission-' + permission.id">{{ permission.name }}</Label>
                                    </div>
                                </div>
                                <InputError :message="form.errors.permissions" />
                            </div>
                        </div>

                        <DialogFooter>
                            <Button type="submit" :disabled="form.processing">
                                Créer le rôle
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <div class="rounded-md border dark:border-gray-800">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Rôle</TableHead>
                            <TableHead>Permissions</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="role in roles" :key="role.id">
                            <TableCell>
                                <div class="font-medium">{{ role.name }}</div>
                            </TableCell>
                            <TableCell>
                                <div class="grid gap-2">
                                    <div v-for="permission in permissions" :key="permission.id" class="flex items-center space-x-2">
                                        <Checkbox
                                            :id="'role-' + role.id + '-permission-' + permission.id"
                                            :checked="role.permissions.includes(permission.id)"
                                            @change="updateRolePermissions(role, permission)"
                                        />
                                        <Label :for="'role-' + role.id + '-permission-' + permission.id">
                                            {{ permission.name }}
                                        </Label>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Button
                                    v-if="role.name !== 'admin'"
                                    variant="destructive"
                                    size="sm"
                                    @click="deleteRole(role)"
                                >
                                    Supprimer
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </section>
</template> 