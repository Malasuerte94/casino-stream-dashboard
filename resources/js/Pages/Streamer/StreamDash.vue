<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StreamBoard from './Components/StreamBoard.vue';
import ChangeLog from './Components/ChangeLog.vue';
</script>

<template>
  <AppLayout title="Dashboard">
    <!-- Main Content -->
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <transition name="fade" mode="out-in">
          <div class="bg-gray-800 shadow-xl sm:rounded-lg p-6">
            <StreamBoard />
            <div class="my-8"></div>
            <ChangeLog />
          </div>
        </transition>
      </div>
    </div>
  </AppLayout>
</template>
