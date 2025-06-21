<template>
  <Sidebar>
    <SidebarHeader>
      <SidebarMenuButton as-child>
        <Link :href="route('admin.dashboard')" class="flex items-center gap-2">
          <Shield class="h-6 w-6" />
          <span class="font-semibold">Administration</span>
        </Link>
      </SidebarMenuButton>
    </SidebarHeader>

    <SidebarContent>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton as-child :active="$page.url === route('admin.dashboard')">
            <Link :href="route('admin.dashboard')" class="flex items-center gap-2">
              <LayoutDashboard class="h-4 w-4" />
              <span>Dashboard</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>

        <SidebarMenuItem>
          <SidebarMenuButton as-child :active="$page.url.startsWith(route('admin.users.index'))">
            <Link :href="route('admin.users.index')" class="flex items-center gap-2">
              <Users class="h-4 w-4" />
              <span>Users</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>

        <SidebarMenuItem>
          <SidebarMenuButton as-child :active="$page.url.startsWith(route('admin.categories.index'))">
            <Link :href="route('admin.categories.index')" class="flex items-center gap-2">
              <Tags class="h-4 w-4" />
              <span>Categories</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarContent>

    <SidebarFooter>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton as-child>
            <Link :href="route('home')" class="flex items-center gap-2">
              <ArrowLeft class="h-4 w-4" />
              <span>Back to App</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarFooter>
  </Sidebar>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { 
  Shield, 
  LayoutDashboard, 
  Users, 
  Tags, 
  ArrowLeft 
} from 'lucide-vue-next'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'

const $page = usePage()
</script> 