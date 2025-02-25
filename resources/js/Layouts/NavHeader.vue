<!-- resources/js/Components/NavHeader.vue -->
<template>
  <nav class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/10 relative z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <!-- Logo -->
          <div class="shrink-0 flex items-center">
            <Link :href="route('user.dashboard')">
              <ApplicationMark class="block h-9 w-auto" />
            </Link>
          </div>
          <!-- Navigation Links -->
          <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex" v-if="$page.props.user.id">
            <NavLink
                :href="$page.props.user_streamer ? route('streamer.dashboard') : route('user.dashboard')"
                :active="$page.props.user_streamer ? route().current('streamer.dashboard') : route().current('user.dashboard')"
                class="text-gray-300 hover:text-white transition-colors duration-200"
            >
              Dashboard
            </NavLink>
            <NavLink
                v-if="!$page.props.user_streamer"
                :href="route('user.streamer-list')"
                :active="route().current('user.streamer-list')"
                class="text-gray-300 hover:text-white transition-colors duration-200"
            >
              Streamers
            </NavLink>
            <!-- Streamer-specific items -->
            <template v-if="$page.props.user_streamer">
              <NavLink
                  :href="route('streamer.lists')"
                  :active="route().current('streamer.lists')"
                  class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                Bonus Lists
              </NavLink>
              <NavLink
                  :href="route('streamer.bonus-battle')"
                  :active="route().current('streamer.bonus-battle')"
                  class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                Bonus Battle
              </NavLink>
              <NavLink
                  :href="route('streamer.wheel-settings')"
                  :active="route().current('streamer.wheel-settings')"
                  class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                Wheel Config
              </NavLink>
              <NavLink
                  :href="route('streamer.schedule')"
                  :active="route().current('streamer.schedule')"
                  class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                Schedule
              </NavLink>
              <NavLink
                  :href="route('streamer.report')"
                  :active="route().current('streamer.report')"
                  class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                Raport
              </NavLink>
              <NavLink
                  :href="route('view-streamer', { id: $page.props.user.id })"
                  :active="route().current('view-streamer')"
                  class="text-orange-600 font-bold hover:text-white transition-colors duration-200"
              >
                Streamer Public Page
              </NavLink>
            </template>
          </div>
        </div>

        <!-- Right Side: User Avatar Dropdown or Login/Register -->
        <div class="hidden sm:flex sm:items-center sm:ml-6">
          <div v-if="$page.props.user.id" class="ml-3 relative">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button
                    v-if="$page.props.jetstream.managesProfilePhotos"
                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-500 transition"
                >
                  <img
                      class="h-8 w-8 rounded-full object-cover"
                      :src="$page.props.user.profile_photo_url"
                      :alt="$page.props.user.name"
                  />
                </button>
                <span v-else class="inline-flex rounded-md">
                  <button
                      type="button"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-white focus:outline-none focus:bg-gray-700 active:bg-gray-700 transition"
                  >
                    {{ $page.props.user.name }}
                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                  </button>
                </span>
              </template>
              <template #content>
                <div class="block px-4 py-2 text-xs text-gray-400">Manage Account</div>
                <DropdownLink :href="route('profile.show')">Profile</DropdownLink>
                <template v-if="$page.props.user_streamer">
                  <DropdownLink :href="route('streamer.stream-accounts')" :active="route().current('streamer.stream-accounts')">
                    Conturi Stream
                  </DropdownLink>
                  <DropdownLink :href="route('streamer.banners')" :active="route().current('streamer.banners')">
                    Banners
                  </DropdownLink>
                  <DropdownLink :href="route('streamer.settings')" :active="route().current('streamer.settings')">
                    OBS Settings
                  </DropdownLink>
                </template>
                <div class="border-t border-gray-700"></div>
                <form @submit.prevent="logout">
                  <DropdownLink as="button">Log Out</DropdownLink>
                </form>
              </template>
            </Dropdown>
          </div>
          <div v-else class="flex items-center gap-2">
            <NavLink>Logare / Modal</NavLink>
            <NavLink>Înregistrare / Modal</NavLink>
          </div>
        </div>

        <!-- Hamburger (Mobile) -->
        <div class="-mr-2 flex items-center sm:hidden">
          <button
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition"
              @click="showingNavigationDropdown = !showingNavigationDropdown"
          >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path
                  :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                  :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }" class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink
            :href="$page.props.user_streamer ? route('streamer.dashboard') : route('user.dashboard')"
            :active="$page.props.user_streamer ? route().current('streamer.dashboard') : route().current('user.dashboard')"
            class="text-gray-300 hover:text-white transition-colors duration-200"
        >
          Dashboard
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('user.streamer-list')"
            :active="route().current('user.streamer-list')"
            class="text-gray-300 hover:text-white transition-colors duration-200"
        >
          Streamers
        </ResponsiveNavLink>
        <!-- Streamer-specific items -->
        <template v-if="$page.props.user_streamer">
          <ResponsiveNavLink
              :href="route('streamer.lists')"
              :active="route().current('streamer.lists')"
              class="text-gray-300 hover:text-white transition-colors duration-200"
          >
            Bonus Lists
          </ResponsiveNavLink>
          <ResponsiveNavLink
              :href="route('streamer.bonus-battle')"
              :active="route().current('streamer.bonus-battle')"
              class="text-gray-300 hover:text-white transition-colors duration-200"
          >
            Bonus Battle
          </ResponsiveNavLink>
          <ResponsiveNavLink
              :href="route('streamer.wheel-settings')"
              :active="route().current('streamer.wheel-settings')"
              class="text-gray-300 hover:text-white transition-colors duration-200"
          >
            Wheel Config
          </ResponsiveNavLink>
          <ResponsiveNavLink
              :href="route('streamer.schedule')"
              :active="route().current('streamer.schedule')"
              class="text-gray-300 hover:text-white transition-colors duration-200"
          >
            Schedule
          </ResponsiveNavLink>
          <ResponsiveNavLink
              :href="route('streamer.report')"
              :active="route().current('streamer.report')"
              class="text-gray-300 hover:text-white transition-colors duration-200"
          >
            Raport
          </ResponsiveNavLink>
          <ResponsiveNavLink
              :href="route('view-streamer', { id: $page.props.user.id })"
              :active="route().current('view-streamer')"
              class="text-orange-600 font-bold hover:text-white transition-colors duration-200"
          >
            Streamer Public Page
          </ResponsiveNavLink>
        </template>
      </div>
      <!-- User-specific options -->
      <div v-if="$page.props.user.id" class="pt-4 pb-1 border-t border-gray-700">
        <div class="flex items-center px-4">
          <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
            <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
          </div>
          <div>
            <div class="font-medium text-base text-gray-200">{{ $page.props.user.name }}</div>
            <div class="font-medium text-sm text-gray-400">{{ $page.props.user.email }}</div>
          </div>
        </div>
        <div class="mt-3 space-y-1">
          <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
            Profile
          </ResponsiveNavLink>
          <template v-if="$page.props.user_streamer">
            <ResponsiveNavLink :href="route('streamer.stream-accounts')" :active="route().current('streamer.stream-accounts')">
              Conturi Stream
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('streamer.banners')" :active="route().current('streamer.banners')">
              Banners
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('streamer.settings')" :active="route().current('streamer.settings')">
              OBS Settings
            </ResponsiveNavLink>
          </template>
          <form @submit.prevent="logout">
            <ResponsiveNavLink as="button">Log Out</ResponsiveNavLink>
          </form>
        </div>
      </div>
      <!-- Guest options -->
      <div v-else class="pt-4 pb-1 border-t border-gray-700">
        <div class="mt-3 space-y-1">
          <ResponsiveNavLink :href="route('user.dashboard')">Logare</ResponsiveNavLink>
          <ResponsiveNavLink :href="route('register')">Înregistrare</ResponsiveNavLink>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);

const logout = () => {
  router.post(route('logout'));
};
</script>