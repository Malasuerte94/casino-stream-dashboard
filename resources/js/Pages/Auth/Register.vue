<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

</script>
<template>
    <Head title="Register" />
    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>
            <div class="flex items-center justify-between">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Ai cont deja?
                </Link>
                <a href="/auth/youtube/redirect" class="ml-4 youtube-register">
                  Înregistrare cu YouTube
                </a>
            </div>
    </AuthenticationCard>
</template>
