<template>
  <DropdownMenu>
    <DropdownMenuTrigger asChild>
      <Button variant="outline" class="w-[200px] justify-between">
        <div class="flex items-center gap-2">
          <span class="truncate">{{ currentOrganization?.name }}</span>
        </div>
        <ChevronDownIcon class="h-4 w-4 opacity-50" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-[200px]">
      <DropdownMenuLabel>Organisations</DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuGroup>
        <DropdownMenuItem
          v-for="org in organizations"
          :key="org.id"
          @click="switchOrganization(org)"
          :class="{ 'bg-accent': org.id === currentOrganization?.id }"
        >
          <div class="flex items-center gap-2">
            <span class="truncate">{{ org.name }}</span>
            <CheckIcon
              v-if="org.id === currentOrganization?.id"
              class="ml-auto h-4 w-4"
            />
          </div>
        </DropdownMenuItem>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuItem @click="createOrganization">
        <PlusIcon class="mr-2 h-4 w-4" />
        <span>Nouvelle organisation</span>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { CheckIcon, ChevronDownIcon, PlusIcon } from '@heroicons/vue/24/outline'

const page = usePage()
const organizations = computed(() => page.props.auth.user?.organizations)
const currentOrganization = computed(() => page.props.auth.user?.current_organization)

const switchOrganization = (organization) => {
  router.post(route('organizations.switch', organization), {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      window.location.reload()
    }
  })
}

const createOrganization = () => {
  router.visit(route('organizations.create'))
}
</script> 