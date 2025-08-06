<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import NavLink from '@/components/NavLink.vue'
import UserDropdown from '@/components/UserDropdown.vue'
import CartMenu from '@/components/CartMenu.vue'
import { Button } from '@/components/ui/button'
import TopbarSearch from '@/components/TopbarSearch.vue'
import { 
  Plus,
  LayoutDashboard,
  Box,
  Menu,
  X,
  User,
  Settings,
  LogOut,
  CalendarDays,
  Clock
} from 'lucide-vue-next'

const page = usePage()
const user = computed(() => page.props.auth.user)

// Mobile menu state
const isMobileMenuOpen = ref(false)

// Navigation items
const navigationItems = computed(() => [
  {
    name: 'Tableau de bord',
    href: route('app.dashboard'),
    active: route().current('app.dashboard'),
    icon: LayoutDashboard,
    show: !!user.value
  },
  {
    name: 'Réservations',
    href: route('app.organizations.reservations.in.index'),
    active: route().current('app.organizations.reservations.in.index'),
    icon: CalendarDays,
    show: !!user.value
  },
  {
    name: 'Agenda',
    href: route('app.organizations.calendar'),
    active: route().current('app.organizations.calendar'),
    icon: Clock,
    show: !!user.value
  },
  {
    name: 'Matériel',
    href: route('app.organizations.equipments.index'),
    active: route().current('app.organizations.equipments.index'),
    icon: Box,
    show: !!user.value
  }
])
</script>

<template>
  <nav class="border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between min-h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <Link :href="route('home')" class="flex-shrink-0 flex items-center">
            <h1 class="flex items-baseline">
              <span class="text-3xl font-bold font-title bg-gradient-to-r from-violet-600 to-violet-400 bg-clip-text text-transparent">
                asso
              </span>
              <span class="text-3xl font-bold font-title bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent relative">
                cation
                <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-gradient-to-r from-violet-400 to-blue-600 rounded-full transform scale-x-100 transition-transform"></span>
              </span>
            </h1>
          </Link>

          <!-- Desktop Navigation -->
          <div class="hidden md:ml-6 md:flex md:space-x-4">
            <Button asChild v-if="!user" size="sm">
              <Link :href="route('app.organizations.equipments.create')" class="gap-2 flex items-center">
                <Plus class="h-4 w-4" />
                Ajouter votre matériel
              </Link>
            </Button>

            <div
              v-for="item in navigationItems"
              v-show="item.show"
              :key="item.href"
              class="flex items-center gap-2"
            >
              <NavLink
                :href="item.href"
                :active="item.active"
                :icon="item.icon"
              >
                {{ item.name }}
              </NavLink>
              
              <!-- CTA Button for Matériel -->
              <Button
                v-if="item.name === 'Matériel'"
                asChild
                size="sm"
                variant="secondary"
                class="h-8 w-8 p-0 rounded-full"
                title="Ajouter votre matériel"
              >
                <Link :href="route('app.organizations.equipments.create')">
                  <Plus class="h-4 w-4" />
                </Link>
              </Button>
            </div>
          </div>
        </div>

        <!-- Desktop Right Section -->
        <div class="hidden md:flex md:items-center md:space-x-4">
          <!-- Search (if needed) -->
          <TopbarSearch v-if="user && !route().current('home')" />
          
          <!-- Auth Navigation -->
          <template v-if="user">
            <UserDropdown />
          </template>
          <template v-else>
            <NavLink :href="route('login')" size="sm">
              Connexion
            </NavLink>
            <Button asChild variant="default" size="sm">
              <Link :href="route('register')">
                Inscription
              </Link>
            </Button>
          </template>

          <!-- Cart -->
          <CartMenu />
        </div>

        <!-- Mobile header actions -->
        <div class="flex items-center space-x-2 md:hidden">
          <!-- Cart for mobile -->
          <CartMenu />
          
          <!-- Mobile menu button -->
          <button
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
          >
            <span class="sr-only">{{ isMobileMenuOpen ? 'Fermer le menu' : 'Ouvrir le menu' }}</span>
            <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
            <X v-else class="h-6 w-6" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div
      v-show="isMobileMenuOpen"
      class="md:hidden border-t border-gray-200"
    >
      <!-- User info section for mobile -->
      <div v-if="user" class="px-4 py-3 bg-gray-50">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
              <span class="text-primary-700 font-medium">
                {{ user.name.charAt(0).toUpperCase() }}
              </span>
            </div>
          </div>
          <div class="ml-3">
            <div class="text-base font-medium text-gray-800">{{ user.name }}</div>
            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
          </div>
        </div>
      </div>

      <div class="pt-2 pb-3 space-y-1">
        <Button v-if="!user" asChild size="sm">
          <Link :href="route('app.organizations.equipments.create')">
            <Plus class="h-4 w-4" />
            Ajouter votre matériel
          </Link>
        </Button>

        <a
          v-for="item in navigationItems"
          v-show="item.show"
          :key="item.href"
          :href="item.href"
          class="flex items-center gap-2 px-4 py-2 text-base font-medium transition duration-150 ease-in-out"
          :class="[
            item.active
              ? 'text-primary-700 bg-primary-50'
              : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'
          ]"
        >
          <component :is="item.icon" class="h-5 w-5" />
          {{ item.name }}
        </a>
      </div>

      <!-- Mobile auth navigation -->
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="space-y-1">
          <template v-if="user">
            <!-- User specific actions -->
            <Link
              :href="route('profile.edit')"
              class="flex items-center gap-2 px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50"
            >
              <User class="h-5 w-5" />
              Mon profil
            </Link>
            <Link
              :href="route('app.organizations.settings.edit')"
              class="flex items-center gap-2 px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50"
            >
              <Settings class="h-5 w-5" />
              Paramètres de l'organisation
            </Link>
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="flex items-center gap-2 w-full px-4 py-2 text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50"
            >
              <LogOut class="h-5 w-5" />
              Se déconnecter
            </Link>
          </template>
          <template v-else>
            <Link
              :href="route('login')"
              class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50"
            >
              Connexion
            </Link>
            <Link
              :href="route('register')"
              class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50"
            >
              Inscription
            </Link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>