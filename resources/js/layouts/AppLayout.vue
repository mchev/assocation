<script setup>
import { Head } from '@inertiajs/vue3'
import Header from '@/layouts/partials/Header.vue'
import Footer from '@/layouts/partials/Footer.vue'
import FlashMessage from '@/components/FlashMessage.vue'

defineProps({
  title: {
    type: String,
    required: false
  },
  description: {
    type: String,
    required: false
  }
})
</script>

<template>
  <div class="min-h-screen">
    <Head>
      <title>{{ title }}</title>
      <meta head-key="description" name="description" :content="description" />
    </Head>

    <Header />
    <FlashMessage />

    <!-- Page Heading -->
    <header class="dark:bg-muted/50 shadow" v-if="$slots.header">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 font-title">
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
