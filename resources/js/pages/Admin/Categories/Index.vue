<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Categories</h1>
          <p class="text-muted-foreground">
            Manage equipment categories and subcategories
          </p>
        </div>
        <Button as-child>
          <Link :href="route('admin.categories.create')">
            <Plus class="h-4 w-4 mr-2" />
            Add Category
          </Link>
        </Button>
      </div>

      <!-- Categories List -->
      <Card>
        <CardHeader>
          <CardTitle>All Categories</CardTitle>
          <CardDescription>
            A hierarchical list of all equipment categories
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-for="category in categories" :key="category.id" class="space-y-2">
              <!-- Main Category -->
              <div class="flex items-center space-x-4 p-3 rounded-lg border">
                <Tags class="h-5 w-5 text-muted-foreground" />
                <div class="space-y-1 flex-1">
                  <p class="text-sm font-medium leading-none">{{ category.name }}</p>
                  <p v-if="category.description" class="text-sm text-muted-foreground">
                    {{ category.description }}
                  </p>
                </div>
                <div class="flex items-center gap-2">
                  <Badge variant="outline">
                    {{ category.children?.length || 0 }} subcategories
                  </Badge>
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button variant="ghost" class="h-8 w-8 p-0">
                        <MoreHorizontal class="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem as-child>
                        <Link :href="route('admin.categories.edit', category.id)">
                          <Pencil class="h-4 w-4 mr-2" />
                          Edit
                        </Link>
                      </DropdownMenuItem>
                      <DropdownMenuSeparator />
                      <DropdownMenuItem 
                        @click="deleteCategory(category.id)"
                        class="text-destructive focus:text-destructive"
                      >
                        <Trash2 class="h-4 w-4 mr-2" />
                        Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              </div>

              <!-- Subcategories -->
              <div v-if="category.children && category.children.length > 0" class="ml-8 space-y-2">
                <div v-for="subcategory in category.children" :key="subcategory.id" class="flex items-center space-x-4 p-3 rounded-lg border bg-muted/50">
                  <Tags class="h-4 w-4 text-muted-foreground" />
                  <div class="space-y-1 flex-1">
                    <p class="text-sm font-medium leading-none">{{ subcategory.name }}</p>
                    <p v-if="subcategory.description" class="text-sm text-muted-foreground">
                      {{ subcategory.description }}
                    </p>
                  </div>
                  <div class="flex items-center gap-2">
                    <DropdownMenu>
                      <DropdownMenuTrigger as-child>
                        <Button variant="ghost" class="h-8 w-8 p-0">
                          <MoreHorizontal class="h-4 w-4" />
                        </Button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="end">
                        <DropdownMenuItem as-child>
                          <Link :href="route('admin.categories.edit', subcategory.id)">
                            <Pencil class="h-4 w-4 mr-2" />
                            Edit
                          </Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem 
                          @click="deleteCategory(subcategory.id)"
                          class="text-destructive focus:text-destructive"
                        >
                          <Trash2 class="h-4 w-4 mr-2" />
                          Delete
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="categories.length === 0" class="text-center py-6">
              <Tags class="h-8 w-8 text-muted-foreground mx-auto mb-2" />
              <p class="text-sm text-muted-foreground">No categories found</p>
            </div>
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
  Tags, 
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

const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
})

const deleteCategory = (categoryId) => {
  if (confirm('Are you sure you want to delete this category?')) {
    router.delete(route('admin.categories.destroy', categoryId))
  }
}
</script> 