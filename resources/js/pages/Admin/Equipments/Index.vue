<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Matériel</h1>
          <p class="text-muted-foreground">
            Liste de tout le matériel référencé. Retrouvez facilement le matériel sans photo.
          </p>
        </div>
      </div>

      <!-- Filters -->
      <Card>
        <CardHeader>
          <CardTitle>Filtres</CardTitle>
          <CardDescription>
            Affiner la liste pour trouver le matériel sans photo ou par recherche
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="applyFilters" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
              <Label for="search">Recherche</Label>
              <Input
                id="search"
                v-model="form.search"
                type="text"
                placeholder="Nom, description, marque..."
                class="mt-1"
              />
            </div>
            <div class="flex items-center gap-2">
              <Checkbox
                id="without_photos"
                v-model:checked="form.without_photos"
              />
              <Label for="without_photos" class="cursor-pointer font-normal">
                Sans photo uniquement
              </Label>
            </div>
            <div class="flex gap-2">
              <Button type="submit">
                <Search class="h-4 w-4 mr-2" />
                Appliquer
              </Button>
              <Button type="button" variant="outline" @click="resetFilters">
                Réinitialiser
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <!-- Equipments List -->
      <Card>
        <CardHeader>
          <CardTitle>Tout le matériel</CardTitle>
          <CardDescription>
            {{ equipments.total }} équipement(s)
            <span v-if="filters.without_photos"> sans photo</span>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div
              v-for="equipment in equipments.data"
              :key="equipment.id"
              class="flex items-center gap-4 p-3 rounded-lg border"
            >
              <div class="shrink-0 w-14 h-14 rounded-md overflow-hidden bg-muted flex items-center justify-center">
                <img
                  v-if="equipment.images?.length"
                  :src="equipment.images[0].url"
                  :alt="equipment.name"
                  class="w-full h-full object-cover"
                />
                <ImageOff v-else class="h-6 w-6 text-muted-foreground" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="font-medium truncate">{{ equipment.name }}</p>
                <p class="text-sm text-muted-foreground truncate">
                  {{ equipment.organization?.name }} · {{ equipment.category?.name }}
                </p>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <Badge v-if="equipment.images_count === 0" variant="destructive">
                  Sans photo
                </Badge>
                <Badge v-else variant="secondary">
                  {{ equipment.images_count }} photo(s)
                </Badge>
                <Button variant="ghost" size="sm" as-child>
                  <Link :href="route('admin.equipments.show', equipment.id)">
                    <Eye class="h-4 w-4 mr-1" />
                    Voir
                  </Link>
                </Button>
              </div>
            </div>

            <div v-if="equipments.data.length === 0" class="text-center py-12">
              <Package class="h-12 w-12 text-muted-foreground mx-auto mb-3" />
              <p class="text-muted-foreground">Aucun matériel trouvé</p>
              <Button variant="outline" class="mt-2" @click="resetFilters">
                Réinitialiser les filtres
              </Button>
            </div>
          </div>

          <div v-if="equipments.data.length > 0" class="mt-6">
            <Pagination :links="equipments.links" />
          </div>
        </CardContent>
      </Card>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import { Package, Search, ImageOff, Eye } from 'lucide-vue-next'
import { watch } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import Pagination from '@/components/Pagination.vue'

const props = defineProps({
  equipments: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const form = useForm({
  search: props.filters.search ?? '',
  without_photos: props.filters.without_photos ?? false,
})

watch(() => props.filters, (f) => {
  form.search = f.search ?? ''
  form.without_photos = f.without_photos ?? false
}, { immediate: true })

const applyFilters = () => {
  const params = {}
  if (form.search) params.search = form.search
  if (form.without_photos) params.without_photos = '1'
  const query = Object.keys(params).length ? '?' + new URLSearchParams(params).toString() : ''
  router.get(route('admin.equipments.index') + query, {}, { preserveState: true })
}

const resetFilters = () => {
  form.search = ''
  form.without_photos = false
  router.get(route('admin.equipments.index'))
}
</script>
