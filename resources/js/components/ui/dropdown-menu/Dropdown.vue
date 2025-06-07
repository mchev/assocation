<template>
  <div class="relative">
    <div @click="open = !open">
      <slot name="trigger" />
    </div>

    <div
      v-show="open"
      class="fixed inset-0 z-40"
      @click="open = false"
    />

    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-show="open"
        class="absolute z-50 mt-2 rounded-md shadow-lg"
        :class="[width === '48' ? 'w-48' : 'w-56']"
        :style="[align === 'left' ? 'left: 0' : 'right: 0']"
        @click="open = false"
      >
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
          <slot name="content" />
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps({
  align: {
    type: String,
    default: 'right'
  },
  width: {
    type: String,
    default: '48'
  }
})

const open = ref(false)
</script> 