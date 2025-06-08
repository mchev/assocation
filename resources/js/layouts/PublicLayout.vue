<script setup>
import Header from '@/layouts/partials/Header.vue'
import Footer from '@/layouts/partials/Footer.vue'
import { Head } from '@inertiajs/vue3'

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

    <main>
      <slot />
    </main>

    <Footer />
  </div>
</template>