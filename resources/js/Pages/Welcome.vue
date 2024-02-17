<script setup>
import {Head, Link} from "@inertiajs/vue3";

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
});
</script>

<template>
  <div
      class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0"
  >
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
      <div class="flex center self-center text-center m-auto justify-center mb-10">
        <h2 class="font-semibold text-xl text-center justify-center text-gray-800 leading-tight mr-2">
          Dashboard Păcănele
        </h2>
        <a target="blank" href="https://www.youtube.com/@MalaTheMan">
          by @MalaTheMan</a
        >
      </div>
      <div class="center text-center">
        <div
            v-if="canLogin"
            class="hidden px-6 py-4 sm:block font-semibold text-lg"
        >

          <div class="flex gap-10 text-center" v-if="$page.props.user">
            <Link
                v-if="$page.props.user_streamer"
                :href="route('streamdash')"
                class="text-lg text-black underline"
            >Dashboard Streamer
            </Link>
            <Link
                :href="route('dashboard')"
                class="text-lg text-black underline text-center m-auto"
            >Dashboard Viewer
            </Link>
          </div>

          <template v-else>
            <Link
                :href="route('login')"
                class="text-lg text-black underline"
            >Log in
            </Link
            >
            <Link
                v-if="canRegister"
                :href="route('register')"
                class="ml-4 text-lg text-black underline"
            >Register
            </Link
            >
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
