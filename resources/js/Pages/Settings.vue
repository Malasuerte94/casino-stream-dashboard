<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsSchedule from '@/Components/SettingsSchedule.vue';
import SettingsBonusList from "@/Components/SettingsBonusList.vue";
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Settings
      </h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <SettingsBonusList/>
          <div class="mb-12"></div>
        </div>
      </div>
    </div>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <SettingsSchedule/>
          <div class="mb-12"></div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>
