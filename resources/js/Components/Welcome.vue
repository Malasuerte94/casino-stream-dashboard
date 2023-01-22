<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import BonusBuy from '@/Components/MainComponents/BonusBuy.vue';
</script>

<template>
    <div>
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div>
                <ApplicationLogo class="block h-12 w-auto" />
            </div>

            <div class="mt-8 text-2xl">
                Welcome to your Jetstream application!
            </div>

            <div class="mt-6 text-gray-500">
                Acest program a fost dezvoltat de Author
            </div>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
            <div class="p-6">
                <BonusBuy />
            </div>

            <div class="p-6 border-t border-gray-200 md:border-l">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                        Authentication
                    </div>
                </div>

                <div class="ml-12">
                    <div class="mt-2 text-sm text-gray-500">
                        Authentication and registration views are included with Laravel Jetstream, as well as support for user email verification and resetting forgotten passwords. So, you're free to get started what matters most: building your application.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
