<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StreamAccountsManager from "./Components/StreamAccountsManager.vue";
</script>

<template>
  <AppLayout title="Dashboard">
    <div class="py-4 transition-all duration-300">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Dark Mode Card Container -->
        <div class="bg-gray-900 overflow-hidden shadow-lg rounded-md transition-all duration-300">
          <StreamAccountsManager />
          <div class="mb-12"></div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
