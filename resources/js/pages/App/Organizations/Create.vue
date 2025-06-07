<template>
  <AppLayout title="Nouvelle organisation">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Nouvelle organisation
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Nom -->
              <div>
                <Label for="name">Nom</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full"
                  required
                  autofocus
                />
                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
              </div>

              <!-- Description -->
              <div class="sm:col-span-2">
                <Label for="description">Description</Label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  class="mt-1 block w-full"
                  rows="3"
                />
                <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
              </div>

              <!-- Adresse -->
              <div class="sm:col-span-2">
                <Label for="address">Adresse</Label>
                <Input
                  id="address"
                  v-model="form.address"
                  type="text"
                  class="mt-1 block w-full"
                />
                <p v-if="form.errors.address" class="mt-2 text-sm text-red-600">{{ form.errors.address }}</p>
              </div>

              <!-- Ville -->
              <div>
                <Label for="city">Ville</Label>
                <Input
                  id="city"
                  v-model="form.city"
                  type="text"
                  class="mt-1 block w-full"
                />
                <p v-if="form.errors.city" class="mt-2 text-sm text-red-600">{{ form.errors.city }}</p>
              </div>

              <!-- Code postal -->
              <div>
                <Label for="postal_code">Code postal</Label>
                <Input
                  id="postal_code"
                  v-model="form.postal_code"
                  type="text"
                  class="mt-1 block w-full"
                />
                <p v-if="form.errors.postal_code" class="mt-2 text-sm text-red-600">{{ form.errors.postal_code }}</p>
              </div>

              <!-- Téléphone -->
              <div>
                <Label for="phone">Téléphone</Label>
                <Input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  class="mt-1 block w-full"
                />
                <p v-if="form.errors.phone" class="mt-2 text-sm text-red-600">{{ form.errors.phone }}</p>
              </div>

              <!-- Email -->
              <div>
                <Label for="email">Email</Label>
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full"
                />
                <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
              </div>

              <!-- Site web -->
              <div class="sm:col-span-2">
                <Label for="website">Site web</Label>
                <Input
                  id="website"
                  v-model="form.website"
                  type="url"
                  class="mt-1 block w-full"
                />
                <p v-if="form.errors.website" class="mt-2 text-sm text-red-600">{{ form.errors.website }}</p>
              </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
              <Link
                :href="route('organizations.index')"
                class="text-sm font-semibold leading-6 text-gray-900"
              >
                Annuler
              </Link>
              <Button
                type="submit"
                :disabled="form.processing"
                :class="{ 'opacity-25': form.processing }"
              >
                Créer l'organisation
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'

const form = useForm({
  name: '',
  description: '',
  address: '',
  city: '',
  postal_code: '',
  phone: '',
  email: '',
  website: '',
})

const submit = () => {
  form.post(route('organizations.store'))
}
</script> 