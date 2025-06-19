<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import Header from '@/layouts/partials/Header.vue'
import Footer from '@/layouts/partials/Footer.vue'
import FlashMessage from '@/components/FlashMessage.vue'

const page = usePage()

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
      <meta v-if="description" head-key="description" name="description" :content="description" />
      <meta v-if="title" head-key="og:title" property="og:title" :content="title" />
      <link rel="canonical" head-key="canonical" :href="page.props.canonical" />
      <meta v-if="description" head-key="og:description" property="og:description" :content="description" />
      <meta head-key="og:image" property="og:image" :content="page.props.app_url + '/android-chrome-512x512.png'" />
      <meta head-key="og:url" property="og:url" :content="page.props.canonical" />
      <meta head-key="og:type" property="og:type" content="website" />
      <meta head-key="og:site_name" property="og:site_name" :content="page.props.name" />
      <meta head-key="og:locale" property="og:locale" content="fr_FR" />
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
