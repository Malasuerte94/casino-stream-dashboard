<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StreamBoard from '@/Components/MainComponents/StreamBoard.vue';
import NavLink from '@/Components/NavLink.vue'
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex gap-2">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-2">Dashboard Păcănele</h2>
                <NavLink target="blank" :href="route('stream-start', { id: $page.props.user.id  })" :active="route().current('stream-start')">
                    Casino + Depo - OBS
                </NavLink>
                <NavLink :href="route('bonus-list', { id: $page.props.user.id })" :active="route().current('bonus-list')">
                    Bonus Buy - OBS
                </NavLink>
                <NavLink :href="route('banners-obs', { id: $page.props.user.id })" :active="route().current('banners-obs')">
                    Banners - OBS
                </NavLink>
                <a class="self-end w-auto" style="margin-left: auto;" target="blank" href="https://www.youtube.com/@MalaTheMan"> by @MalaTheMan</a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <StreamBoard/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
