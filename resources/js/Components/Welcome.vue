<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import BonusBuy from '@/Components/MainComponents/BonusBuy.vue';
import StreamBoard from '@/Components/MainComponents/StreamBoard.vue';
</script>

<template>
    <div>
        <div class="p-6 bg-white border-b border-gray-200">
            <div>
                <ApplicationLogo class="block h-12 w-auto" />
            </div>

            <div class="mt-2 text-2xl">
                Welcome to your APS Dashboard
            </div>

            <div class="mt-2 text-gray-500">
                Acest program a fost dezvoltat de <a target="blank" href="https://www.youtube.com/@MalaTheMan">@MalaTheMan</a>
            </div>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
            <div class="p-6">
                <StreamBoard />
            </div>
            <div class="p-6">
                <BonusBuy />
            </div>

            <div class="p-6 border-t border-gray-200 md:border-l">
                Bonus Hunt (soon)
            </div>
        </div>
    </div>
</template>
