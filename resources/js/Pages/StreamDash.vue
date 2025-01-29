<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StreamBoard from '@/Components/MainComponents/StreamBoard.vue';
import NavLink from '@/Components/NavLink.vue';
import ChangeLog from '@/Components/MainComponents/ChangeLog.vue';
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <div class="flex flex-wrap gap-2 bg-gray-800 p-2 rounded-lg shadow h-10 items-center">
        <span class="text-white text-sm font-bold px-3">OBS</span>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent hover:text-amber-500 transition"
                 :href="route('bonus-list', { id: $page.props.user.id })"
                 :active="route().current('bonus-list')">
          Bonus Buy
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent hover:text-amber-500 transition"
                 :href="route('bonus-battle-view', { id: $page.props.user.id })"
                 :active="route().current('bonus-battle-view')">
          Bonus Battle
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent hover:text-amber-500 transition"
                 :href="route('bonus-winner-obs', { id: $page.props.user.id })"
                 :active="route().current('bonus-winner-obs')">
          Bonus Winner
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent hover:text-amber-500 transition"
                 :href="route('yt-like-view-counter', { id: $page.props.user.id })"
                 :active="route().current('yt-like-view-counter')">
          Like / View Counter YT
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent hover:text-amber-500 transition"
                 :href="route('picker-wheel', { id: $page.props.user.id })"
                 :active="route().current('picker-wheel')">
          Wheel
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent hover:text-amber-500 transition"
                 :href="route('banners-obs', { id: $page.props.user.id })"
                 :active="route().current('banners-obs')">
          Banners
        </NavLink>
        <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent hover:text-amber-500 transition"
                 :href="route('schedule-view', { id: $page.props.user.id })"
                 :active="route().current('schedule-view')">
          Schedule
        </NavLink>
      </div>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <StreamBoard/>
          <div class="mb-12"></div>
          <ChangeLog/>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
