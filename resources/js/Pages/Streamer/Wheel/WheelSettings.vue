<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import WheelLists from "./Components/WheelLists.vue";
</script>

<template>
  <AppLayout title="Dashboard">
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Card Container -->
        <div class="bg-gray-800 overflow-hidden shadow-lg rounded-md transition-all duration-300 p-6">
            <WheelLists />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Dark mode base overrides */
.bg-white {
  @apply bg-gray-900;
}
.shadow-xl {
  @apply shadow-lg;
}
.sm\:rounded-lg {
  @apply rounded-md;
}

/* Card container transition */
.transition-all {
  transition: all 0.3s ease;
}
</style>
