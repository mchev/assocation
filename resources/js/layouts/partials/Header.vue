<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import NavLink from '@/components/NavLink.vue'
import OrganizationDropdown from '@/components/OrganizationDropdown.vue'
import UserDropdown from '@/components/UserDropdown.vue'
import CartMenu from '@/components/CartMenu.vue'
import SearchInput from '@/components/Home/SearchInput.vue'
import { Button } from '@/components/ui/button'
import { PlusIcon } from 'lucide-vue-next'

const page = usePage()
const user = page.props.auth.user
</script>

<template>
<nav class="border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between py-2">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="shrink-0 flex items-center">
            <Link :href="route('home')">
              <h1 class="text-4xl font-bold font-title text-blue-500">assodepot</h1>
            </Link>
          </div>

          <!-- App Navigation Links -->
          <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">

            <!-- Call to action ajouter un equipement -->
            <Button
                :href="route('app.equipments.create')"
                variant="default"
                size="sm"
                class="gap-2"
            >
                <PlusIcon class="h-4 w-4" />
                Ajouter votre matériel
            </Button>
            
            <NavLink v-if="user" :href="route('app.dashboard')" :active="route().current('app.dashboard')">
              Dashboard
            </NavLink>

            
          </div>
          
        </div>

        <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
          <!-- Organization Switcher -->
          <div class="relative" v-if="user">
            <OrganizationDropdown
              :user="user"
            />
          </div>

          <!-- User Dropdown -->
          <div class="relative" v-if="user">
            <UserDropdown 
              :user="user" 
            />
          </div>
          <div class="space-x-8 sm:-my-px sm:ml-10 sm:flex" v-else>
              <NavLink :href="route('login')">
                  Connexion
              </NavLink>
              <NavLink :href="route('register')">
                  Inscription
              </NavLink>
          </div>

          <!-- Cart Menu -->
          <div class="relative">
            <CartMenu />
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>