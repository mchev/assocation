<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Create User</h1>
          <p class="text-muted-foreground">
            Add a new user to the platform
          </p>
        </div>
        <Button variant="outline" as-child>
          <Link :href="route('admin.users.index')">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Users
          </Link>
        </Button>
      </div>

      <!-- Create Form -->
      <Card>
        <CardHeader>
          <CardTitle>User Information</CardTitle>
          <CardDescription>
            Fill in the details to create a new user account
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
              <div class="space-y-2">
                <Label for="name">Name</Label>
                <Input 
                  id="name" 
                  v-model="form.name" 
                  type="text" 
                  placeholder="Enter full name"
                  :class="{ 'border-destructive': form.errors.name }"
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">
                  {{ form.errors.name }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input 
                  id="email" 
                  v-model="form.email" 
                  type="email" 
                  placeholder="Enter email address"
                  :class="{ 'border-destructive': form.errors.email }"
                />
                <p v-if="form.errors.email" class="text-sm text-destructive">
                  {{ form.errors.email }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="password">Password</Label>
                <Input 
                  id="password" 
                  v-model="form.password" 
                  type="password" 
                  placeholder="Enter password"
                  :class="{ 'border-destructive': form.errors.password }"
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">
                  {{ form.errors.password }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="organization">Organization</Label>
                <Select v-model="form.organization_id">
                  <SelectTrigger :class="{ 'border-destructive': form.errors.organization_id }">
                    <SelectValue placeholder="Select an organization" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem 
                      v-for="org in organizations" 
                      :key="org.id" 
                      :value="org.id"
                    >
                      {{ org.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="form.errors.organization_id" class="text-sm text-destructive">
                  {{ form.errors.organization_id }}
                </p>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <Checkbox 
                id="is_admin" 
                v-model:checked="form.is_admin"
              />
              <Label for="is_admin">Admin privileges</Label>
            </div>

            <div class="flex justify-end space-x-2">
              <Button type="button" variant="outline" as-child>
                <Link :href="route('admin.users.index')">
                  Cancel
                </Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <Loader2 v-if="form.processing" class="h-4 w-4 mr-2 animate-spin" />
                Create User
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Loader2 } from 'lucide-vue-next'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const props = defineProps({
  organizations: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  organization_id: '',
  is_admin: false,
})

const submit = () => {
  form.post(route('admin.users.store'))
}
</script> 