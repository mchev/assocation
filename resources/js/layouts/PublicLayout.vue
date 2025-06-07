<script setup>
import Header from '@/layouts/partials/Header.vue'
import Footer from '@/layouts/partials/Footer.vue'
</script> 
<template>
  <div class="min-h-screen">

    <Header />

    <main>
      <slot />
    </main>

    <Footer />
  </div>
</template>