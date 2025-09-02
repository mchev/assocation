<script setup>
import { computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuGroup } from '@/components/ui/dropdown-menu'
import { 
    ChevronDownIcon,
    Cog6ToothIcon,
    UserIcon,
    ArrowRightOnRectangleIcon,
    BuildingOfficeIcon
} from '@heroicons/vue/24/outline'
import OrganizationSwitcher from '@/components/OrganizationSwitcher.vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const currentOrganization = computed(() => page.props.auth.user?.current_organization)

const goToSettings = () => {
    router.visit(route('app.organizations.settings.edit'))
}
</script>

<template>
    <DropdownMenu v-if="user">
        <DropdownMenuTrigger as-child>
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition ease-in-out duration-150">
                <div class="flex items-center gap-3">             
                    <div class="flex flex-col items-start">
                        <span class="text-sm font-medium">{{ currentOrganization?.name }}</span>
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
                <OrganizationSwitcher />
            </DropdownMenuGroup>

            <!-- Organization Actions -->
            <DropdownMenuSeparator />
            <DropdownMenuItem v-if="currentOrganization" @click="goToSettings" class="cursor-pointer">
                <Cog6ToothIcon class="mr-2 h-4 w-4" />
                <span>Paramètres de l'organisation</span>
            </DropdownMenuItem>

            <!-- User Actions -->
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child>
                <Link :href="route('profile.edit')" class="w-full">
                    <UserIcon class="mr-2 h-4 w-4" />
                    Mon compte
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