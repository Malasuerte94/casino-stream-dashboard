<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const props = defineProps({
  title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
  router.put(route('current-team.update'), { team_id: team.id }, { preserveState: false });
};

const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <div>
    <Head :title="props.title" />
    <Banner />

    <div class="min-h-screen bg-gradient-to-br from-indigo-800 via-gray-800 to-indigo-900">
      <!-- Main Navigation -->
      <nav class="bg-gray-800 border-b border-gray-700">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="shrink-0 flex items-center">
                <Link :href="route('streamdash')">
                  <ApplicationMark class="block h-9 w-auto" />
                </Link>
              </div>
              <!-- Navigation Links -->
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('streamdash')"
                    :active="route().current('streamdash')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Dashboard
                </NavLink>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('lists')"
                    :active="route().current('lists')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Bonus Lists
                </NavLink>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('bonus-battle')"
                    :active="route().current('bonus-battle')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Bonus Battle
                </NavLink>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('wheel-settings')"
                    :active="route().current('wheel-settings')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Wheel Config
                </NavLink>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('schedule')"
                    :active="route().current('schedule')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Schedule
                </NavLink>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('report')"
                    :active="route().current('report')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Raport
                </NavLink>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('referrals', { id: $page.props.user.id })"
                    :active="route().current('referrals')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Referrals
                </NavLink>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <NavLink
                    :href="route('view-streamer', { id: $page.props.user.id })"
                    :active="route().current('view-streamer')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Streamer Public Page
                </NavLink>
              </div>
            </div>

            <!-- Right Side: Teams & Settings Dropdowns -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
              <!-- Teams Dropdown -->
              <div class="ml-3 relative">
                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700 transition"
                      >
                        {{ $page.props.user.current_team.name }}
                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <div class="w-60">
                      <!-- Team Management -->
                      <template v-if="$page.props.jetstream.hasTeamFeatures">
                        <div class="block px-4 py-2 text-xs text-gray-400">
                          Manage Team
                        </div>
                        <!-- Team Settings -->
                        <DropdownLink
                            :href="route('teams.show', $page.props.user.current_team)"
                            class="text-gray-300 hover:text-white transition-colors duration-200"
                        >
                          Team Settings
                        </DropdownLink>
                        <DropdownLink
                            v-if="$page.props.jetstream.canCreateTeams"
                            :href="route('teams.create')"
                            class="text-gray-300 hover:text-white transition-colors duration-200"
                        >
                          Create New Team
                        </DropdownLink>
                        <div class="border-t border-gray-700"></div>
                        <!-- Team Switcher -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                          Switch Teams
                        </div>
                        <template v-for="team in $page.props.user.all_teams" :key="team.id">
                          <form @submit.prevent="switchToTeam(team)">
                            <DropdownLink
                                as="button"
                                class="text-gray-300 hover:text-white transition-colors duration-200"
                            >
                              <div class="flex items-center">
                                <svg
                                    v-if="team.id == $page.props.user.current_team_id"
                                    class="mr-2 h-5 w-5 text-green-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                  <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>{{ team.name }}</div>
                              </div>
                            </DropdownLink>
                          </form>
                        </template>
                      </template>
                    </div>
                  </template>
                </Dropdown>
              </div>

              <!-- Settings Dropdown -->
              <div class="ml-3 relative">
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
                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Manage Account
                    </div>
                    <DropdownLink
                        :href="route('profile.show')"
                        class="text-gray-300 hover:text-white transition-colors duration-200"
                    >
                      Profile
                    </DropdownLink>
                    <DropdownLink
                        v-if="$page.props.jetstream.hasApiFeatures"
                        :href="route('api-tokens.index')"
                        class="text-gray-300 hover:text-white transition-colors duration-200"
                    >
                      API Tokens
                    </DropdownLink>
                    <DropdownLink
                        :href="route('stream-accounts')"
                        :active="route().current('stream-accounts')"
                        class="text-gray-300 hover:text-white transition-colors duration-200"
                    >
                      Conturi Stream
                    </DropdownLink>
                    <DropdownLink
                        :href="route('banners')"
                        :active="route().current('banners')"
                        class="text-gray-300 hover:text-white transition-colors duration-200"
                    >
                      Banners
                    </DropdownLink>
                    <DropdownLink
                        :href="route('settings')"
                        :active="route().current('settings')"
                        class="text-gray-300 hover:text-white transition-colors duration-200"
                    >
                      OBS Settings
                    </DropdownLink>
                    <div class="border-t border-gray-700"></div>
                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                      <DropdownLink
                          as="button"
                          class="text-gray-300 hover:text-white transition-colors duration-200"
                      >
                        Log Out
                      </DropdownLink>
                    </form>
                  </template>
                </Dropdown>
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
                      :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                      :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
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
        <div
            :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}"
            class="sm:hidden"
        >
          <div class="pt-2 pb-3 space-y-1">
            <ResponsiveNavLink
                :href="route('streamdash')"
                :active="route().current('streamdash')"
                class="text-gray-300 hover:text-white transition-colors duration-200"
            >
              Dashboard
            </ResponsiveNavLink>
          </div>

          <!-- Responsive Settings Options -->
          <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="flex items-center px-4">
              <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                <img
                    class="h-10 w-10 rounded-full object-cover"
                    :src="$page.props.user.profile_photo_url"
                    :alt="$page.props.user.name"
                />
              </div>
              <div>
                <div class="font-medium text-base text-gray-200">
                  {{ $page.props.user.name }}
                </div>
                <div class="font-medium text-sm text-gray-400">
                  {{ $page.props.user.email }}
                </div>
              </div>
            </div>

            <div class="mt-3 space-y-1">
              <ResponsiveNavLink
                  :href="route('profile.show')"
                  :active="route().current('profile.show')"
                  class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                Profile
              </ResponsiveNavLink>
              <ResponsiveNavLink
                  v-if="$page.props.jetstream.hasApiFeatures"
                  :href="route('api-tokens.index')"
                  :active="route().current('api-tokens.index')"
                  class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                API Tokens
              </ResponsiveNavLink>
              <!-- Authentication -->
              <form method="POST" @submit.prevent="logout">
                <ResponsiveNavLink as="button" class="text-gray-300 hover:text-white transition-colors duration-200">
                  Log Out
                </ResponsiveNavLink>
              </form>
              <!-- Team Management -->
              <template v-if="$page.props.jetstream.hasTeamFeatures">
                <div class="border-t border-gray-700"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">
                  Manage Team
                </div>
                <!-- Team Settings -->
                <ResponsiveNavLink
                    :href="route('teams.show', $page.props.user.current_team)"
                    :active="route().current('teams.show')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Team Settings
                </ResponsiveNavLink>
                <ResponsiveNavLink
                    v-if="$page.props.jetstream.canCreateTeams"
                    :href="route('teams.create')"
                    :active="route().current('teams.create')"
                    class="text-gray-300 hover:text-white transition-colors duration-200"
                >
                  Create New Team
                </ResponsiveNavLink>
                <div class="border-t border-gray-700"></div>
                <!-- Team Switcher -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                  Switch Teams
                </div>
                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                  <form @submit.prevent="switchToTeam(team)">
                    <ResponsiveNavLink as="button" class="text-gray-300 hover:text-white transition-colors duration-200">
                      <div class="flex items-center">
                        <svg
                            v-if="team.id == $page.props.user.current_team_id"
                            class="mr-2 h-5 w-5 text-green-400"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>{{ team.name }}</div>
                      </div>
                    </ResponsiveNavLink>
                  </form>
                </template>
              </template>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header v-if="$slots.header" class="bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>