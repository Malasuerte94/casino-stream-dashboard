<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsSchedule from "./Components/SettingsSchedule.vue";
import SettingsBonusList from "./Components/SettingsBonusList.vue";
import SettingsBonusBattle from "./Components/SettingsBonusBattle.vue";
</script>

<template>
  <AppLayout title="Dashboard">
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <SettingsBonusList/>
          <div class="mb-12"></div>
        </div>
      </div>
    </div>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <SettingsSchedule/>
          <div class="mb-12"></div>
        </div>
      </div>
    </div>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <SettingsBonusBattle/>
          <div class="mb-12"></div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>
