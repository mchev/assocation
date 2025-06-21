<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Users</h1>
          <p class="text-muted-foreground">
            Manage all users on the platform
          </p>
        </div>
        <Button as-child>
          <Link :href="route('admin.users.create')">
            <Plus class="h-4 w-4 mr-2" />
            Add User
          </Link>
        </Button>
      </div>

      <!-- Users Table -->
      <Card>
        <CardHeader>
          <CardTitle>All Users</CardTitle>
          <CardDescription>
            A list of all registered users on the platform
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-for="user in users.data" :key="user.id" class="flex items-center space-x-4">
              <div class="space-y-1 flex-1">
                <p class="text-sm font-medium leading-none">{{ user.name }}</p>
                <p class="text-sm text-muted-foreground">{{ user.email }}</p>
              </div>
              <div class="flex items-center gap-2">
                <Badge v-if="user.is_admin" variant="default">
                  Admin
                </Badge>
                <Badge v-else variant="secondary">
                  User
                </Badge>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" class="h-8 w-8 p-0">
                      <MoreHorizontal class="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem as-child>
                      <Link :href="route('admin.users.edit', user.id)">
                        <Pencil class="h-4 w-4 mr-2" />
                        Edit
                      </Link>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem 
                      @click="deleteUser(user.id)"
                      class="text-destructive focus:text-destructive"
                    >
                      <Trash2 class="h-4 w-4 mr-2" />
                      Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </div>
            
            <div v-if="users.data.length === 0" class="text-center py-6">
              <Users class="h-8 w-8 text-muted-foreground mx-auto mb-2" />
              <p class="text-sm text-muted-foreground">No users found</p>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="users.data.length > 0" class="mt-6">
            <Pagination :links="users.links" />
          </div>
        </CardContent>
      </Card>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { 
  Plus, 
  Users, 
  MoreHorizontal, 
  Pencil, 
  Trash2 
} from 'lucide-vue-next'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import Pagination from '@/components/Pagination.vue'

defineProps({
  users: {
    type: Object,
    required: true,
  },
})

const deleteUser = (userId) => {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(route('admin.users.destroy', userId))
  }
}
</script> 