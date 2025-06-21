<template>
  <SidebarProvider>
    <div class="flex min-h-screen w-full">
      <AdminSidebar />
      
      <div class="flex-1 flex flex-col">
        <!-- Top navigation bar -->
        <nav class="bg-white border-b border-gray-100 px-4 py-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <SidebarTrigger class="md:hidden" />
              <h1 class="text-lg font-semibold text-gray-900">
                {{ pageTitle }}
              </h1>
            </div>
            <UserDropdown />
          </div>
        </nav>

        <!-- Main content -->
        <main class="flex-1 bg-gray-50 p-6">
          <div class="max-w-7xl mx-auto">
            <slot />
          </div>
        </main>
      </div>
    </div>
  </SidebarProvider>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'
import AdminSidebar from '@/components/AdminSidebar.vue'
import UserDropdown from '@/components/UserDropdown.vue'

const $page = usePage()

const pageTitle = computed(() => {
  const url = $page.url
  
  if (url === route('admin.dashboard')) {
    return 'Dashboard'
  }
  
  if (url.startsWith(route('admin.users.index'))) {
    return 'Users'
  }
  
  if (url.startsWith(route('admin.categories.index'))) {
    return 'Categories'
  }
  
  return 'Administration'
})
</script> 