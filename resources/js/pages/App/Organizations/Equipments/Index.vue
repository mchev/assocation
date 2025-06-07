<template>
  <AppLayout :title="'Matériel - ' + organization.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Matériel
        </h2>
        <Button
          v-if="can.create"
          :href="route('organizations.equipment.create', organization)"
          variant="default"
          size="default"
        >
          Ajouter du matériel
        </Button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtres -->
        <Card class="mb-6">
          <CardContent class="p-6">
            <form @submit.prevent="filter" class="space-y-4">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="space-y-2">
                  <Label for="search">Rechercher</Label>
                  <Input
                    id="search"
                    v-model="filters.search"
                    type="text"
                    placeholder="Nom, catégorie..."
                  />
                </div>

                <div class="space-y-2">
                  <Label for="category">Catégorie</Label>
                  <Select v-model="filters.category">
                    <SelectTrigger>
                      <SelectValue placeholder="Toutes les catégories" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="">Toutes les catégories</SelectItem>
                      <SelectItem v-for="category in categories" :key="category" :value="category">
                        {{ category }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div class="space-y-2">
                  <Label for="condition">État</Label>
                  <Select v-model="filters.condition">
                    <SelectTrigger>
                      <SelectValue placeholder="Tous les états" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="">Tous les états</SelectItem>
                      <SelectItem value="new">Neuf</SelectItem>
                      <SelectItem value="good">Bon</SelectItem>
                      <SelectItem value="fair">Moyen</SelectItem>
                      <SelectItem value="poor">Mauvais</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div class="space-y-2">
                  <Label for="availability">Disponibilité</Label>
                  <Select v-model="filters.availability">
                    <SelectTrigger>
                      <SelectValue placeholder="Tous" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="">Tous</SelectItem>
                      <SelectItem value="available">Disponible</SelectItem>
                      <SelectItem value="unavailable">Non disponible</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div class="flex justify-end">
                <Button type="submit" variant="default">
                  Filtrer
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <!-- Liste du matériel -->
        <Card>
          <CardContent class="p-6">
            <div v-if="equipment.length === 0" class="text-center py-12">
              <p class="text-muted-foreground text-lg">
                Aucun matériel trouvé
              </p>
              <Button
                v-if="can.create"
                :href="route('organizations.equipment.create', organization)"
                variant="default"
                class="mt-4"
              >
                Ajouter du matériel
              </Button>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              <Card v-for="item in equipment" :key="item.id" class="overflow-hidden">
                <CardContent class="p-6">
                  <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">
                      {{ item.name }}
                    </h3>
                    <Badge
                      :variant="item.is_available ? 'success' : 'destructive'"
                    >
                      {{ item.is_available ? 'Disponible' : 'Non disponible' }}
                    </Badge>
                  </div>

                  <p class="mt-2 text-sm text-muted-foreground">
                    {{ item.category }}
                  </p>

                  <p class="mt-2 text-sm line-clamp-2">
                    {{ item.description }}
                  </p>

                  <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                      <Badge
                        :variant="{
                          new: 'default',
                          good: 'success',
                          fair: 'warning',
                          poor: 'destructive'
                        }[item.condition]"
                      >
                        {{ {
                          new: 'Neuf',
                          good: 'Bon',
                          fair: 'Moyen',
                          poor: 'Mauvais'
                        }[item.condition] }}
                      </Badge>

                      <Badge
                        v-if="item.is_rentable"
                        variant="secondary"
                      >
                        Louable
                      </Badge>
                    </div>

                    <div class="flex items-center space-x-2">
                      <Button
                        :href="route('organizations.equipment.show', [organization, item])"
                        variant="ghost"
                        size="sm"
                      >
                        Voir
                      </Button>

                      <Button
                        v-if="can.update"
                        :href="route('organizations.equipment.edit', [organization, item])"
                        variant="ghost"
                        size="sm"
                      >
                        Modifier
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  equipment: {
    type: Array,
    required: true
  },
  filters: {
    type: Object,
    required: true
  },
  can: {
    type: Object,
    required: true
  }
})

const categories = computed(() => {
  return [...new Set(props.equipment.map(item => item.category))]
})

const filters = useForm({
  search: props.filters?.search ?? '',
  category: props.filters?.category ?? '',
  condition: props.filters?.condition ?? '',
  availability: props.filters?.availability ?? ''
})

const filter = () => {
  filters.get(route('organizations.equipment.index', props.organization), {
    preserveState: true,
    preserveScroll: true
  })
}
</script> 