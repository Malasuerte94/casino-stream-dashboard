<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BonusLists from '@/Components/BonusLists.vue';
</script>

<template>
    <AppLayout title="Bonuses">
        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        Poți alege între <b>Hunt</b> sau <b>Buy</b> și lista se va actualiza automat în OBS, pentru a beneficia de toate funcționalitățile te rog să introduci miza, prețul dacă este buy și costul total dacă este hunt.
                    </div>
                    <BonusLists />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
