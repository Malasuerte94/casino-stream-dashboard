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
  <div class="min-h-screen bg-gray-900 text-gray-100 flex flex-col">
    <!-- Header -->
    <header class="bg-gray-800 py-4 shadow-md">
      <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Dashboard Păcănele</h1>
        <a
            target="_blank"
            href="https://www.youtube.com/@MalaTheMan"
            class="text-sm hover:underline"
        >
          by @MalaTheMan
        </a>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center">
      <div class="text-center">
        <div v-if="canLogin">
          <!-- If user is logged in -->
          <div v-if="$page.props.user" class="flex gap-6 justify-center">
            <!-- Display Dashboard Streamer only if user is a streamer -->
            <Link
                v-if="$page.props.user_streamer"
                :href="route('streamdash')"
                class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
            >
              Dashboard Streamer
            </Link>
            <Link
                :href="route('dashboard')"
                class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
            >
              Dashboard Viewer
            </Link>
          </div>
          <!-- If user is not logged in -->
          <div v-else class="flex gap-6 justify-center">
            <Link
                :href="route('login')"
                class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
            >
              Log in
            </Link>
            <Link
                v-if="canRegister"
                :href="route('register')"
                class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
            >
              Register
            </Link>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
