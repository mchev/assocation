<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                    <p class="text-muted-foreground">
                        Welcome to your admin dashboard. Here's an overview of your platform.
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.users_count }}</div>
                        <p class="text-xs text-muted-foreground">
                            Registered users on the platform
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Organizations</CardTitle>
                        <Building2 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.organizations_count }}</div>
                        <p class="text-xs text-muted-foreground">
                            Active organizations
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Equipment</CardTitle>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.equipment_count }}</div>
                        <p class="text-xs text-muted-foreground">
                            Available equipment items
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Data -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Recent Users -->
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Users</CardTitle>
                        <CardDescription>
                            Latest registered users on the platform
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="user in recent_users" :key="user.id" class="flex items-center space-x-4">
                                <Avatar class="h-9 w-9">
                                    <AvatarImage v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" />
                                    <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                                </Avatar>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">{{ user.name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                                </div>
                                <div class="ml-auto font-medium">
                                    <Badge variant="secondary">
                                        {{ user.organizations?.length || 0 }} orgs
                                    </Badge>
                                </div>
                            </div>
                            <div v-if="recent_users.length === 0" class="text-center py-6">
                                <Users class="h-8 w-8 text-muted-foreground mx-auto mb-2" />
                                <p class="text-sm text-muted-foreground">No users found</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Organizations -->
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Organizations</CardTitle>
                        <CardDescription>
                            Latest organizations that joined the platform
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="org in recent_organizations" :key="org.id" class="flex items-center space-x-4">
                                <Avatar class="h-9 w-9">
                                    <AvatarImage v-if="org.logo_url" :src="org.logo_url" :alt="org.name" />
                                    <AvatarFallback>{{ getInitials(org.name) }}</AvatarFallback>
                                </Avatar>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">{{ org.name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ org.city }}, {{ org.state }}</p>
                                </div>
                                <div class="ml-auto font-medium">
                                    <Badge variant="outline">
                                        {{ org.type || 'Organization' }}
                                    </Badge>
                                </div>
                            </div>
                            <div v-if="recent_organizations.length === 0" class="text-center py-6">
                                <Building2 class="h-8 w-8 text-muted-foreground mx-auto mb-2" />
                                <p class="text-sm text-muted-foreground">No organizations found</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Users, Building2, Package } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recent_users: {
        type: Array,
        required: true,
    },
    recent_organizations: {
        type: Array,
        required: true,
    },
})

const getInitials = (name) => {
    if (!name) return '?'
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2)
}
</script>