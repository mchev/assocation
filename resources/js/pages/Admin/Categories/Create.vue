<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Create Category</h1>
          <p class="text-muted-foreground">
            Add a new equipment category
          </p>
        </div>
        <Button variant="outline" as-child>
          <Link :href="route('admin.categories.index')">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Categories
          </Link>
        </Button>
      </div>

      <!-- Create Form -->
      <Card>
        <CardHeader>
          <CardTitle>Category Information</CardTitle>
          <CardDescription>
            Fill in the details to create a new category
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
                  placeholder="Enter category name"
                  :class="{ 'border-destructive': form.errors.name }"
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">
                  {{ form.errors.name }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="parent_id">Parent Category (Optional)</Label>
                <Select v-model="form.parent_id">
                  <SelectTrigger :class="{ 'border-destructive': form.errors.parent_id }">
                    <SelectValue placeholder="Select parent category" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="">No parent (Top level)</SelectItem>
                    <SelectItem 
                      v-for="category in categories" 
                      :key="category.id" 
                      :value="category.id"
                    >
                      {{ category.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="form.errors.parent_id" class="text-sm text-destructive">
                  {{ form.errors.parent_id }}
                </p>
              </div>
            </div>

            <div class="space-y-2">
              <Label for="description">Description</Label>
              <Textarea 
                id="description" 
                v-model="form.description" 
                placeholder="Enter category description"
                :class="{ 'border-destructive': form.errors.description }"
                rows="3"
              />
              <p v-if="form.errors.description" class="text-sm text-destructive">
                {{ form.errors.description }}
              </p>
            </div>

            <div class="space-y-2">
              <Label for="order">Display Order</Label>
              <Input 
                id="order" 
                v-model="form.order" 
                type="number" 
                placeholder="Enter display order"
                :class="{ 'border-destructive': form.errors.order }"
              />
              <p v-if="form.errors.order" class="text-sm text-destructive">
                {{ form.errors.order }}
              </p>
            </div>

            <div class="flex justify-end space-x-2">
              <Button type="button" variant="outline" as-child>
                <Link :href="route('admin.categories.index')">
                  Cancel
                </Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <Loader2 v-if="form.processing" class="h-4 w-4 mr-2 animate-spin" />
                Create Category
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
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  name: '',
  description: '',
  parent_id: '',
  order: '',
})

const submit = () => {
  form.post(route('admin.categories.store'))
}
</script> 