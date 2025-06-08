<script setup>
import { computed } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuGroup } from '@/components/ui/dropdown-menu'
import { 
    CheckIcon, 
    ChevronDownIcon, 
    PlusIcon,
    Cog6ToothIcon,
    UserIcon,
    ArrowRightOnRectangleIcon,
    BuildingOfficeIcon
} from '@heroicons/vue/24/outline'

const page = usePage()
const user = computed(() => page.props.auth.user)
const organizations = computed(() => page.props.auth.user?.organizations)
const currentOrganization = computed(() => page.props.auth.user?.current_organization)

const switchOrganization = (organization) => {
    router.post(route('app.organizations.switch', organization), {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            window.location.reload()
        }
    })
}

const createOrganization = () => {
    router.visit(route('app.organizations.create'))
}

const goToSettings = () => {
    router.visit(route('app.organizations.settings'))
}

// Generate initials for organization avatar
const getOrganizationInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
}
</script>

<template>
    <DropdownMenu v-if="user">
        <DropdownMenuTrigger as-child>
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition ease-in-out duration-150">
                <div class="flex items-center gap-3">             
                    <div class="flex flex-col items-start">
                        <span class="text-sm font-medium text-gray-900">{{ currentOrganization?.name }}</span>
                        <span class="text-xs text-gray-500">{{ user.name }}</span>
                    </div>
                </div>

                <ChevronDownIcon class="ml-2 h-4 w-4 text-gray-500" />
            </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent class="w-[280px]">
            <!-- User Section -->
            <DropdownMenuLabel class="font-normal">
                <div class="flex flex-col space-y-1">
                    <p class="text-sm font-medium leading-none">{{ user.name }}</p>
                    <p class="text-xs leading-none text-gray-500">{{ user.email }}</p>
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            
            <!-- Organizations Section -->
            <DropdownMenuLabel class="flex items-center">
                <BuildingOfficeIcon class="mr-2 h-4 w-4" />
                <span>Changer d'organisation</span>
            </DropdownMenuLabel>
            <DropdownMenuGroup class="max-h-[200px] overflow-y-auto">
                <DropdownMenuItem
                    v-for="org in organizations"
                    :key="org.id"
                    @click="switchOrganization(org)"
                    :class="[
                        'flex items-center gap-2 cursor-pointer',
                        org.id === currentOrganization?.id ? 'bg-primary-50' : 'hover:bg-gray-50'
                    ]"
                >
                    <!-- Organization Avatar -->
                    <div class="flex-shrink-0 h-6 w-6 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-xs font-medium">
                        {{ getOrganizationInitials(org.name) }}
                    </div>
                    <span class="flex-1 truncate">{{ org.name }}</span>
                    <CheckIcon
                        v-if="org.id === currentOrganization?.id"
                        class="ml-auto h-4 w-4 text-primary-600"
                    />
                </DropdownMenuItem>
            </DropdownMenuGroup>

            <!-- Organization Actions -->
            <DropdownMenuSeparator />
            <DropdownMenuItem v-if="currentOrganization" @click="goToSettings" class="cursor-pointer">
                <Cog6ToothIcon class="mr-2 h-4 w-4" />
                <span>Paramètres de l'organisation actuelle</span>
            </DropdownMenuItem>
            <DropdownMenuItem @click="createOrganization" class="cursor-pointer">
                <PlusIcon class="mr-2 h-4 w-4" />
                <span>Créer une nouvelle organisation</span>
            </DropdownMenuItem>

            <!-- User Actions -->
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child>
                <Link :href="route('profile.edit')" class="w-full">
                    <UserIcon class="mr-2 h-4 w-4" />
                    Mon profil
                </Link>
            </DropdownMenuItem>
            <DropdownMenuItem as-child>
                <Link :href="route('logout')" method="post" as="button" class="w-full text-red-600 hover:text-red-700 hover:bg-red-50">
                    <ArrowRightOnRectangleIcon class="mr-2 h-4 w-4" />
                    Se déconnecter
                </Link>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>