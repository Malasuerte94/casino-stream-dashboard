<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StreamBoard from '@/Components/MainComponents/StreamBoard.vue';
import NavLink from '@/Components/NavLink.vue';
import ChangeLog from '@/Components/MainComponents/ChangeLog.vue';
</script>

<template>
  <AppLayout title="Dashboard">
    <!-- Header Navigation -->
    <template #header>
      <div class="flex flex-wrap gap-2 bg-gray-800 p-2 rounded-lg shadow-lg h-10 items-center">
        <span class="text-white text-sm font-bold px-3">OBS Widgets</span>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('bonus-list', { id: $page.props.user.id })"
                 :active="route().current('bonus-list')">
          Bonus Buy
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('bonus-battle-view', { id: $page.props.user.id })"
                 :active="route().current('bonus-battle-view')">
          Bonus Battle
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('bonus-battle-picker', { id: $page.props.user.id })"
                 :active="route().current('bonus-battle-picker')">
          Bonus Battle Picker
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('bonus-winner-obs', { id: $page.props.user.id })"
                 :active="route().current('bonus-winner-obs')">
          Bonus Winner
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('yt-like-view-counter', { id: $page.props.user.id })"
                 :active="route().current('yt-like-view-counter')">
          Like / View Counter YT
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('picker-wheel', { id: $page.props.user.id })"
                 :active="route().current('picker-wheel')">
          Wheel
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('banners-obs', { id: $page.props.user.id })"
                 :active="route().current('banners-obs')">
          Banners
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                 :href="route('schedule-view', { id: $page.props.user.id })"
                 :active="route().current('schedule-view')">
          Schedule
        </NavLink>
      </div>
    </template>

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

<style scoped>
/* Fade Transition for the main card */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
