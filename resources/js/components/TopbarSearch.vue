<script setup>
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Search } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const search = ref('');
const isFocused = ref(false);

const handleSearch = () => {
  if (!search.value.trim()) return;
  
  router.get(route('home'), {
    search: search.value
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const handleKeyDown = (e) => {
  if (e.key === 'Enter') {
    handleSearch();
  }
};
</script>

<template>
  <div class="relative flex items-center">
    <div
      :class="[
        'flex items-center gap-2 transition-all duration-300 ease-in-out',
        isFocused ? 'w-full' : 'w-full'
      ]"
    >
      <div class="relative flex-1">
        <Input
          v-model="search"
          placeholder="Chercher du matériel..."
          class="pr-10 rounded-full"
          @focus="isFocused = true"
          @blur="isFocused = false"
          @keydown="handleKeyDown"
        />
        <Button
          variant="ghost"
          size="icon"
          class="absolute right-0 top-0"
          @click="handleSearch"
        >
          <Search class="h-4 w-4" />
        </Button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-wrapper {
  transition: width 0.3s ease-in-out;
}
</style>