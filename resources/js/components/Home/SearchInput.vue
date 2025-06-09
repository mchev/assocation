<template>

    <div class="flex items-center border rounded-lg shadow-md bg-background">
      <span class="pl-3 text-muted-foreground">
        <Search class="h-5 w-5" />
      </span>
      <input
        ref="inputRef"
        v-model="searchQuery"
        type="text"
        class="flex-1 h-12 px-3 bg-transparent focus:outline-none"
        placeholder="Que recherchez-vous ?"
        @keydown.enter.prevent="onEnter"
        autocomplete="off"
      />
      <button
        v-if="searchQuery"
        @mousedown.prevent="resetSearch"
        class="pr-3 text-muted-foreground hover:text-foreground"
        tabindex="-1"
      >
        <X class="h-4 w-4" />
      </button>
    </div>

</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { Search, X } from 'lucide-vue-next';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
});
const emit = defineEmits(['update:modelValue', 'search:submit']);

const inputRef = ref(null);
const searchQuery = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  }
});

const resetSearch = () => {
  searchQuery.value = '';
  nextTick(() => {
    inputRef.value?.focus();
  });
};

const onEnter = () => {
  emit('search:submit', searchQuery.value);
};

</script>