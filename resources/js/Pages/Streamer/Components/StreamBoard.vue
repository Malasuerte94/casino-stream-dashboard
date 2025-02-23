<template>
  <div class="p-6">

    <transition name="fade">
      <div class="bg-gray-900 p-6 mb-6 rounded border border-red-700 shadow-lg text-center">
        <div class="text-lg text-center mb-4 text-red-300">
          OBS WIDGETS
        </div>
        <div class="flex flex-wrap gap-2 bg-gray-800 p-2 rounded-lg shadow-lg h-10 items-center justify-center">
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('bonus-list', { id: $page.props.user.id })"
                   :active="route().current('bonus-list')">
            Bonus Buy
          </NavLink>
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('bonus-battle-view', { id: $page.props.user.id })"
                   :active="route().current('bonus-battle-view')">
            Bonus Battle
          </NavLink>
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('bonus-battle-picker', { id: $page.props.user.id })"
                   :active="route().current('bonus-battle-picker')">
            Bonus Battle Picker
          </NavLink>
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('bonus-winner-obs', { id: $page.props.user.id })"
                   :active="route().current('bonus-winner-obs')">
            Bonus Winner
          </NavLink>
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('yt-like-view-counter', { id: $page.props.user.id })"
                   :active="route().current('yt-like-view-counter')">
            Like / View Counter YT
          </NavLink>
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('picker-wheel', { id: $page.props.user.id })"
                   :active="route().current('picker-wheel')">
            Wheel
          </NavLink>
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('banners-obs', { id: $page.props.user.id })"
                   :active="route().current('banners-obs')">
            Banners
          </NavLink>
          <NavLink class="text-white text-sm font-semibold px-3 py-1 border-transparent transition-all duration-200 hover:text-amber-500"
                   :href="route('schedule-view', { id: $page.props.user.id })"
                   :active="route().current('schedule-view')">
            Schedule
          </NavLink>
        </div>
      </div>
    </transition>

    <!-- New Stream Call-to-Action Card -->
    <transition name="fade">
      <div class="bg-gray-900 p-6 mb-6 rounded border border-red-700 shadow-lg opacity-25">
        <button
            disabled
            @click="activateDialog"
            type="button"
            class="w-full focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-500 font-medium rounded-lg text-sm px-5 py-2.5 transition-all duration-200"
        >
          Stream Nou
        </button>
        <div class="text-sm mt-4 text-red-300">
          Dacă Vrei să folosești corect și funcția de Retragere / Depunere: Înainte de fiecare stream te rog să apeși butonul de <b>Stream Nou</b>, introdu suma de pe care o ai depozitată pe casino și poți începe, dacă vrei să faci o retragere introdu și retragerea și confirmă pentru a avea un raport cât mai corect.
        </div>
      </div>
    </transition>

    <!-- Deposits and Withdrawals Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <transition name="fade" mode="out-in">
        <div v-if="!loading" class="bg-gray-800 p-6 rounded border border-gray-700 shadow">
          <DepositsStream :stream="stream" />
        </div>
      </transition>
      <transition name="fade" mode="out-in">
        <div v-if="!loading" class="bg-gray-800 p-6 rounded border border-gray-700 shadow">
          <WithdrawalsStream :stream="stream" />
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import DepositsStream from "./DepositsStream.vue";
import WithdrawalsStream from "./WithdrawalsStream.vue";
import NavLink from "../../../Components/NavLink.vue";
export default {
  components: {NavLink, DepositsStream, WithdrawalsStream },
  emits: ["displayOnly"],
  data() {
    return {
      loading: true,
      stream: [],
      settings: [],
    };
  },
  async mounted() {
    await this.getLatestStream();
    await this.getSettings();
    this.loading = false;
  },
  methods: {
    async getLatestStream() {
      try {
        const response = await axios.get("/api/stream");
        this.stream = response.data.stream;
      } catch (error) {
        console.log(error);
      }
    },
    async getSettings() {
      try {
        const response = await axios.get("/api/settings");
        this.settings = response.data.settings;
      } catch (error) {
        console.log(error);
      }
      if (this.settings.bonus_list) {
        this.$emit("displayOnly", this.settings.bonus_list);
      }
    },
    async createNewStream() {
      try {
        const response = await axios.post("/api/stream/new");
        this.stream = response.data.stream;
      } catch (error) {
        console.log(error);
      } finally {
        location.reload();
      }
      await this.getLatestStream();
    },
    activateDialog() {
      this.$dialog({
        message:
            "Ești sigur că vrei să începi un nou stream, acest lucru va reseta listele si resul optiunilor?",
        buttons: ["da", "nu"],
        da: () => {
          this.createNewStream();
        },
        nu: () => {
          console.log("refuse");
        },
      });
    },
  },
};
</script>

<style scoped>
/* Fade Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
