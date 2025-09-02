<template>
  <AppLayout title="Matériel" description="Liste du matériel de l'organisation">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl leading-tight">
          Inventaire du matériel de {{ organization.name }} ({{ equipments.total }})
        </h2>
        <Button
          v-if="can.equipments.create"
          asChild
        >
          <Link :href="route('app.organizations.equipments.create')">
              Ajouter du matériel
          </Link>
        </Button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtres -->
        <Card class="mb-6">
          <CardContent class="p-4">
            <form @submit.prevent="filter">
              <div class="flex flex-wrap items-end gap-6">
                <div>
                  <Label for="search" class="text-sm">Rechercher</Label>
                  <Input
                    id="search"
                    v-model="filters.search"
                    type="text"
                    placeholder="Nom, catégorie..."
                    @input="filter"
                  />
                </div>

                <div>
                  <Label for="category" class="text-sm">Catégorie</Label>
                  <Select v-model="filters.category" @update:modelValue="filter">
                    <SelectTrigger class="h-9">
                      <SelectValue placeholder="Toutes" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem :value="null">Toutes les catégories</SelectItem>
                      <SelectItem v-for="category in allCategories" :key="category.id" :value="category.name">
                        {{ category.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label for="condition" class="text-sm">État</Label>
                  <Select v-model="filters.condition" @update:modelValue="filter">
                    <SelectTrigger class="h-9">
                      <SelectValue placeholder="Tous" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem :value="null">Tous les états</SelectItem>
                      <SelectItem value="new">Neuf</SelectItem>
                      <SelectItem value="good">Bon</SelectItem>
                      <SelectItem value="fair">Moyen</SelectItem>
                      <SelectItem value="poor">Mauvais</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div class="flex gap-2 ml-auto">
                  <Button 
                    type="button" 
                    variant="outline" 
                    @click="resetFilters"
                    size="sm"
                    class="h-9"
                  >
                    Réinitialiser
                  </Button>
                  <Button 
                    type="submit" 
                    variant="default"
                    size="sm"
                    class="h-9"
                  >
                    Filtrer
                  </Button>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>

        <!-- Liste du matériel -->
        <Card>
          <CardContent class="p-6">
            <div v-if="equipments.data.length === 0" class="text-center py-12">
              <p class="text-muted-foreground text-lg">
                Aucun matériel trouvé
              </p>
              <Button
                v-if="can.equipments.create"
                as="a"
                :href="route('app.organizations.equipments.create', organization)"
                variant="default"
                class="mt-4"
              >
                Ajouter du matériel
              </Button>
            </div>

            <div v-else>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('name')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Nom</span>
                        <component
                          :is="getSortIcon('name')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('category')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Catégorie</span>
                        <component
                          :is="getSortIcon('category')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('depot')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Emplacement</span>
                        <component
                          :is="getSortIcon('depot')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('rental_price')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Prix/jour</span>
                        <component
                          :is="getSortIcon('rental_price')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow 
                    v-for="item in equipments.data" 
                    :key="item.id"
                    class="group cursor-pointer hover:bg-muted/50"
                    @click="$inertia.visit(route('app.organizations.equipments.edit', item))"
                  >
                    <TableCell class="font-medium">
                      <div>
                        {{ item.name }}
                        <p class="text-sm text-muted-foreground truncate max-w-40 lg:max-w-96">
                          {{ item.description }}
                        </p>
                      </div>
                    </TableCell>
                    <TableCell>{{ item.category.name }}</TableCell>
                    <TableCell>
                      <div class="max-w-40 flex flex-col">
                        <span class="truncate font-medium">{{ item.depot.name }}</span>
                        <span class="text-xs text-muted-foreground">
                          {{ item.depot.city }}
                        </span>
                      </div>
                    </TableCell>
                    <TableCell>{{ item.rental_price }}€</TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
          </CardContent>
        </Card>

        <!-- Pagination -->
        <div v-if="equipments.links && equipments.links.length > 3" class="mt-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <span class="text-sm text-muted-foreground">
                Affichage de {{ equipments.from }} à {{ equipments.to }} sur {{ equipments.total }} résultats
              </span>
            </div>
            <Pagination :links="equipments.links" />
          </div>
        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger, 
  SelectValue,
} from '@/components/ui/select'
import { ArrowUpDown, ArrowUp, ArrowDown } from 'lucide-vue-next'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  equipments: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    required: true
  },
  allCategories: {
    type: Array,
    required: true
  }
})

const can = computed(() => usePage().props.auth.user.can)

const filters = useForm({
  search: props.filters?.search ?? '',
  category: props.filters?.category ?? null,
  condition: props.filters?.condition ?? null,
  availability: props.filters?.availability ?? null,
  sort: props.filters?.sort ?? 'name',
  direction: props.filters?.direction ?? 'asc'
})

const filter = () => {
  filters.get(route('app.organizations.equipments.index'), {
    preserveState: true,
    preserveScroll: true
  })
}

const resetFilters = () => {
  filters.search = ''
  filters.category = null
  filters.condition = null
  filters.availability = null
  filters.sort = 'name'
  filters.direction = 'asc'
  filter()
}

const sort = (column) => {
  if (filters.sort === column) {
    filters.direction = filters.direction === 'asc' ? 'desc' : 'asc'
  } else {
    filters.sort = column
    filters.direction = 'asc'
  }
  filter()
}

const getSortIcon = (column) => {
  if (filters.sort !== column) return ArrowUpDown
  return filters.direction === 'asc' ? ArrowUp : ArrowDown
}
</script> 