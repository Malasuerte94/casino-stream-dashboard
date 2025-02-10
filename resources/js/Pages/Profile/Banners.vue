<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BannerSettings from '@/Components/MainComponents/BannerSettings.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-grey-500 overflow-hidden shadow-xl sm:rounded-lg">
                    <BannerSettings></BannerSettings>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
