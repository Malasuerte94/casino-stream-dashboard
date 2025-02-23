<!-- resources/js/Components/AppLayout.vue -->
<template>
  <div>
    <Head :title="title" />
    <Banner />
    <div class="min-h-screen">
      <NavHeader />
      <header v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import NavHeader from './NavHeader.vue';

defineProps({
  title: String,
});
</script>