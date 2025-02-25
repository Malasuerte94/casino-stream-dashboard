<script setup>
import { Link } from '@inertiajs/vue3';
import SvgLogo from "/public/storage/assets/branding/logo-long-white.svg";

import RegisterForm from "@/Components/Auth/RegisterForm.vue";
import LoginForm from "@/Components/Auth/LoginForm.vue";
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="py-4 backdrop-blur-xl bg-white/10 shadow-lg shadow-black/10">
      <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
        <SvgLogo class="w-[150px] h-auto text-gray-500 stroke-current" />
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center">
      <div class="text-center">
        <!-- If user is logged in -->
        <div v-if="$page.props.user.id" class="flex gap-6 justify-center">
          <!-- Display Dashboard Streamer only if user is a streamer -->
          <Link
              v-if="$page.props.user_streamer"
              :href="route('streamer.dashboard')"
              class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
          >
            Dashboard Streamer
          </Link>
          <Link
              :href="route('user.dashboard')"
              class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
          >
            Dashboard Viewer
          </Link>
        </div>

        <!-- If user is not logged in -->
        <div v-else class="flex gap-6 justify-center">
          <LoginForm />
          <RegisterForm />
        </div>
      </div>
    </main>
  </div>
</template>
