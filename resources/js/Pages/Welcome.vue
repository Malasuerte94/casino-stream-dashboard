<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div v-if="canLogin" class="hidden px-6 py-4 sm:block font-semibold text-lg">
            <Link v-if="$page.props.user" :href="route('dashboard')" class="text-sm text-white underline">Dashboard</Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm text-black underline">Log in</Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-black underline">Register</Link>
            </template>
        </div>
        </div>
    </div>
</template>
