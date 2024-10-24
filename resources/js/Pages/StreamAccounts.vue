<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StreamAccountsManager from "@/Components/MainComponents/StreamAccountsManager.vue";
</script>

<template>
  <AppLayout title="Dashboard">
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <StreamAccountsManager/>
          <div class="mb-12"></div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
