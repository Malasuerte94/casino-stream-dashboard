<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import WheelLists from "@/Components/MainComponents/WheelLists.vue";
</script>

<template>
  <AppLayout title="Dashboard">
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="mb-12">
            <WheelLists />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
