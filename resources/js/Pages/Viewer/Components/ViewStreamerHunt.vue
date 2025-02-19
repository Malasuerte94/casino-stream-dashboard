<template>
  <div class="flex flex-col md:flex-row justify-center items-stretch gap-4 mb-4">
    <!-- First Box (1/5 width on desktop) -->
    <div v-if="bonusHuntHistory.length > 0"
         class="gap-2 rounded-lg backdrop-blur-xl p-4 bg-white/10 shadow-lg shadow-black/40 border border-white/20 w-full md:w-1/4 flex flex-col justify-between">

      <div class="flex flex-row justify-between items-center">
        <div class="flex flex-row items-center gap-1">
          <div><SvgBackward class="h-5 w-5 text-white stroke-current" /></div>
          <div>ULTIMUL HUNT</div>
        </div>
        <div class="bg-gray-800 px-2 py-1 rounded-lg text-xs font-bold" :class="{ 'text-gray-500': bonusHuntHistory[0].ended, 'text-green-500': !bonusHuntHistory[0].ended }">
          {{ bonusHuntHistory[0].ended ? 'TERMINAT' : 'ACTIV' }}
        </div>
      </div>

      <div class="flex flex-row justify-between items-center font-bold text-xl">
        <div class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded-lg">
          <div><SvgGame class="h-5 w-5 text-indigo-500 fill-current" /></div>
          <div> {{ bonusHuntHistory[0].bonus_hunt_games.length }}</div>
        </div>
        <div class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded-lg">
          <div><SvgMoney class="h-5 w-5 text-green-500" /></div>
          <div>
            {{ bonusHuntHistory[0].start }} {{currency}}
          </div>
        </div>
      </div>

      <div class="flex flex-row items-center gap-1 text-sm">
        <div><SvgCalendar class="w-4 h-4 text-gray-500 stroke-current" /></div>
        <div>{{ convertDate(bonusHuntHistory[0].created_at ) }}</div>
      </div>

    </div>

    <a href="#" class="relative rounded-lg overflow-hidden w-full md:w-1/4 flex group">
      <img src="https://41.media.tumblr.com/efd15be8d41b12a7b0ef17fba27c3e20/tumblr_mqqy59HMaf1qzattso1_1280.jpg"
           alt="Main User"
           class="w-full h-[130px] object-cover transition-all duration-300 group-hover:scale-105">
      <div
          class="absolute inset-0 bg-black/20 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
    </a>
    <!-- Image Containers with Hover Blur -->
    <a href="#" class="relative rounded-lg overflow-hidden w-full md:w-1/4 flex group">
      <img src="https://41.media.tumblr.com/efd15be8d41b12a7b0ef17fba27c3e20/tumblr_mqqy59HMaf1qzattso1_1280.jpg"
           alt="Main User"
           class="w-full h-[130px] object-cover transition-all duration-300 group-hover:scale-105">
      <div
          class="absolute inset-0 bg-black/20 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
    </a>

    <a href="https://stake.com/?tab=register&modal=auth&c=mala" target="_blank"
       class="relative rounded-lg overflow-hidden w-full md:w-1/4 flex group bg-[url(/storage/assets/images/dog-bg.jpg)] bg-center bg-no-repeat bg-cover">

      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-100 transition-all duration-300 p-4 flex items-center justify-center">

        <!-- Animated Stake SVG -->
        <div class="absolute top-0 mr-auto">
          <SvgStakeSite class="w-20 h-20 animated-pump"/>
        </div>

        <div class="w-full text-center relative mt-14">
          <div v-show="activeIndex === 0" class="fade-slide">
            <div class="bg-gray-800 py-1 px-3 rounded font-bold text-lg inline-block">COD: MALA</div>
          </div>

          <div v-show="activeIndex === 1" class="fade-slide">
            <div class="text-sm text-gray-300 bg-gray-800 py-2 px-1 rounded">
              Rakeback activ + Bonus <span class="font-bold">SĂPTĂMÂNAL</span> și <span class="font-bold">LUNAR</span> din ce ai jucat, fără Rulaj!
            </div>
          </div>

          <div v-show="activeIndex === 2" class="fade-slide">
            <div class="text-sm text-gray-300 bg-gray-800 py-2 px-1 rounded">
              Cele mai <span class="font-bold">MARE RTP</span>  <span class="font-bold">98% - 99%</span>!
            </div>
          </div>
        </div>

      </div>
    </a>
  </div>
  <template v-if="bonusHuntHistory.length > 0">
    <LatestHunt :latestHunt="bonusHuntHistory[0]" :currency="currency" />
    <HuntHistory :bonusHuntHistory="bonusHuntHistory" :currency="currency" />
  </template>
</template>
<script>
import axios from "axios";
import SvgCalendar from "/public/storage/assets/images/calendar.svg";
import SvgGame from "/public/storage/assets/images/slots.svg";
import SvgMoney from "/public/storage/assets/images/money.svg";
import SvgStakeSite from "/public/storage/assets/images/stake-site.svg";
import SvgBackward from "/public/storage/assets/images/backward.svg";
import HuntHistory from "./HuntHistory.vue";
import LatestHunt from "./LatestHunt.vue";

export default {
  components: {
    LatestHunt,
    HuntHistory,
    SvgCalendar,
    SvgGame,
    SvgMoney,
    SvgBackward,
    SvgStakeSite
  },
  data() {
    return {
      streamer: {},
      bonusHuntHistory: [],
      refreshInterval: null,
      currency: 'RON',
      activeIndex: 0,
    };
  },
  props: {
    steamerId: {
      type: Number,
      required: true
    }
  },
  async mounted() {
    await this.getData();
    this.refreshInterval = setInterval(() => {
      this.getBonusHuntHistory();
    }, 5000);
    this.startAutoScroll();
  },
  beforeUnmount() {
    clearInterval(this.refreshInterval);
  },
  methods: {
    async getData() {
      await this.getBonusHuntHistory();
      await this.getSettings();
    },
    async getBonusHuntHistory() {
      try {
        const response = await axios.get("/api/viewer/get-bh-history/" + this.steamerId);
        this.bonusHuntHistory = response.data.bonusHunts;
      } catch (error) {
        console.error("Error fetching bonus hunt history:", error);
      }
    },
    async getSettings() {
      let settingName = "obs_bonus_list";
      try {
        this.loading = true;
        const response = await axios.get(`/api/get-setting-public`, {
          params: {setting_name: settingName, user_id: this.steamerId},
        });
        if (response.data.setting_value) {
          this.currency = JSON.parse(response.data.setting_value).currency;
        }
      } catch (error) {
        console.error(`Error loading setting "${settingName}":`, error);
        this.error = `Failed to load setting: ${settingName}`;
      } finally {
        this.loading = false;
      }
    },
    convertDate(isoString) {
      const date = new Date(isoString);
      return new Intl.DateTimeFormat("ro-RO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
      }).format(date);
    },
    startAutoScroll() {
      setInterval(() => {
        this.activeIndex = (this.activeIndex + 1) % 3;
      }, 5000);
    }
  },
};
</script>
<style scoped>
/* Smooth Fade & Slide Transition */
.fade-slide {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeSlide 1s ease forwards;
}

@keyframes fadeSlide {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Animated Stake SVG Pump Effect */
.animated-pump {
  animation: pumpEffect 2s infinite ease-in-out;
}

@keyframes pumpEffect {
  0%, 100% {
    transform: scale(1);
    filter: drop-shadow(0px 0px 5px red);
  }
  50% {
    transform: scale(1.1);
    filter: drop-shadow(0px 0px 10px yellow);
  }
}
</style>