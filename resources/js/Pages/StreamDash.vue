<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StreamBoard from '@/Components/MainComponents/StreamBoard.vue';
import NavLink from '@/Components/NavLink.vue'
import ChangeLog from '@/Components/MainComponents/ChangeLog.vue'
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex gap-2">
                <NavLink class="text-green-700 font-bold" target="blank" :href="route('stream-start', { id: $page.props.user.id  })" :active="route().current('stream-start')">
                    Casino + Depo - OBS
                </NavLink>
                <NavLink class="text-green-700 font-bold" :href="route('bonus-list', { id: $page.props.user.id })" :active="route().current('bonus-list')">
                    Bonus Buy - OBS
                </NavLink>
                <NavLink class="text-green-700 font-bold" :href="route('banners-obs', { id: $page.props.user.id })" :active="route().current('banners-obs')">
                    Banners - OBS
                </NavLink>
                <NavLink class="text-green-700 font-bold" :href="route('bonus-winner-obs', { id: $page.props.user.id })" :active="route().current('banners-obs')">
                      Bonus Winner - OBS
                </NavLink>
                <NavLink class="text-green-700 font-bold" :href="route('yt-like-view-counter', { id: $page.props.user.id })" :active="route().current('yt-like-view-counter')">
                  Like / View Counter YT - OBS
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
