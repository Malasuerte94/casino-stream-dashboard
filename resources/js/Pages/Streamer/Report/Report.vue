<template>
  <AppLayout title="Report">
    <div class="py-4 transition-all duration-300">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-lg rounded-md transition-all duration-300">
          <ReportUser />
          <ReportBonusHunt />
          <ReportBonusBuy />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportUser from './Components/ReportUser.vue';
import ReportBonusHunt from './Components/ReportBonusHunt.vue';
import ReportBonusBuy from './Components/ReportBonusBuy.vue';
export default {
  data() {
    return {
      stream: [],
      settings: [],
    };
  },
  components: {AppLayout, ReportUser, ReportBonusHunt, ReportBonusBuy}
}
</script>

<style scoped>
/* Smooth Transition */
.transition-all {
  transition: all 0.3s ease;
}
</style>
