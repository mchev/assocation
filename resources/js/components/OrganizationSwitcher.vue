<script setup>
import { computed } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import { Check as CheckIcon, Plus } from 'lucide-vue-next'

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
    <div v-if="user && organizations" class="space-y-1">
        <button
            v-for="org in organizations"
            :key="org.id"
            @click="switchOrganization(org)"
            class="flex items-center gap-2 w-full px-3 py-2 text-sm font-medium transition duration-150 ease-in-out cursor-pointer"
            :class="[
                org.id === currentOrganization?.id
                    ? 'text-primary-700 bg-primary-50'
                    : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'
            ]"
        >
            <div class="h-6 w-6 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-xs font-medium">
                {{ getOrganizationInitials(org.name) }}
            </div>
            <span class="flex-1 text-left">{{ org.name }}</span>
            <CheckIcon
                v-if="org.id === currentOrganization?.id"
                class="h-4 w-4 text-primary-600"
            />
        </button>
        
        <!-- Create Organization Button -->
        <Link
            :href="route('app.organizations.create')"
            class="flex items-center gap-2 w-full px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 cursor-pointer"
        >
            <div class="h-6 w-6 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center text-xs font-medium">
                <Plus class="h-3 w-3" />
            </div>
            <span class="flex-1 text-left">Créer une organisation</span>
        </Link>
    </div>
</template>
