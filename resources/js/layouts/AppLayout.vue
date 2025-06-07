<script setup>
import Header from '@/layouts/partials/Header.vue'
import Footer from '@/layouts/partials/Footer.vue'
</script>

<template>
  <div class="min-h-screen">
    <Header />

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main>
      <slot />
    </main>

    <Footer />
  </div>
</template>
