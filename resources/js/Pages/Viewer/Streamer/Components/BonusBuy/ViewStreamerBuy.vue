<template>
  <div v-if="!loading">
    <div class="flex flex-col md:flex-row justify-center items-stretch gap-4 mb-4">
      <!-- First Box (1/5 width on desktop) -->
      <div v-if="bonusBuyHistory.length > 0"
           class="gap-2 rounded-lg backdrop-blur-xl p-4 bg-white/10 shadow-lg shadow-black/40 border border-white/20 w-full md:w-1/4 flex flex-col justify-between">

        <div class="flex flex-row justify-between items-center">
          <div class="flex flex-row items-center gap-1">
            <div>
              <SvgBackward class="h-5 w-5 text-white stroke-current"/>
            </div>
            <div class="uppercase">{{ $t('latest_buy') }}</div>
          </div>
          <div class="bg-gray-800 px-2 py-1 rounded-lg text-xs font-bold"
               :class="{ 'text-gray-500': latestBuy.ended, 'text-green-500': !latestBuy.ended }">
            {{ latestBuy.ended ? 'TERMINAT' : 'ACTIV' }}
          </div>
        </div>

        <div class="flex flex-row justify-between items-center font-bold text-xl">
          <div class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded-lg">
            <div>
              <SvgGame class="h-5 w-5 text-indigo-500 fill-current"/>
            </div>
            <div> {{ latestBuy.bonus_buy_games.length }}</div>
          </div>
          <div class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded-lg">
            <div>
              <SvgMoney class="h-5 w-5 text-green-500"/>
            </div>
            <div>
              {{ latestBuy.start }} {{ currency }}
            </div>
          </div>
        </div>

        <div class="flex flex-row items-center justify-between gap-1 text-sm">
          <div class="flex flex-row items-center gap-1">
            <div>
              <SvgCalendar class="w-4 h-4 text-gray-500 stroke-current"/>
            </div>
            <div>{{ convertDate(latestBuy.created_at) }}</div>
          </div>
          <div
              class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded"
              :class="{
              'text-green-500': parseFloat(latestBuy.result) >= parseFloat(latestBuy.start),
              'text-red-500': parseFloat(latestBuy.result) <= parseFloat(latestBuy.start)
            }"
          >
            <div>
              <SvgMoney class="h-4 w-4"/>
            </div>
            <div>{{ latestBuy.result }} {{ currency }}</div>
          </div>
        </div>
      </div>


      <template v-if="this.bannerSpot.length >= 2">
        <a :href="this.bannerSpot[0].url" target="_blank"
           class="relative rounded-lg overflow-hidden w-full md:w-1/4 flex group"
           @click="registerClick(this.bannerSpot[0].id)">
          <img :src="this.bannerSpot[0].image"
               :alt="this.bannerSpot[0].name"
               class="w-full h-[130px] object-cover transition-all duration-300 group-hover:scale-105">
          <div
              class="absolute inset-0 bg-black/20 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
        </a>
        <!-- Image Containers with Hover Blur -->
        <a :href="this.bannerSpot[1].url" target="_blank"
           class="relative rounded-lg overflow-hidden w-full md:w-1/4 flex group"
           @click="registerClick(this.bannerSpot[1].id)">
          <img :src="this.bannerSpot[1].image"
               :alt="this.bannerSpot[1].name"
               class="w-full h-[130px] object-cover transition-all duration-300 group-hover:scale-105">
          <div
              class="absolute inset-0 bg-black/20 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
        </a>
      </template>

      <a href="https://stake.com/?tab=register&modal=auth&c=4cb4a7ab68" target="_blank"
         class="relative rounded-lg overflow-hidden w-full md:w-1/4 h-[130px] flex group bg-[url(/storage/assets/images/dog-bg.jpg)] bg-center bg-no-repeat bg-cover">

        <div
            class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-100 transition-all duration-300 p-4 flex items-center justify-center">

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
                Rakeback activ + Bonus <span class="font-bold">SĂPTĂMÂNAL</span> și <span class="font-bold">LUNAR</span>
                din ce ai jucat, fără Rulaj!
              </div>
            </div>

            <div v-show="activeIndex === 2" class="fade-slide">
              <div class="text-sm text-gray-300 bg-gray-800 py-2 px-1 rounded">
                Cele mai <span class="font-bold">MARE RTP</span> <span class="font-bold">98% - 99%</span>!
              </div>
            </div>
          </div>

        </div>
      </a>
    </div>
    <template v-if="bonusBuyHistory.length > 0">
      <transition name="fade" mode="out-in">
        <LatestBuy :buyId="latestBuy.id" :currency="currency"/>
      </transition>
      <transition name="fade" mode="out-in">
        <BuyHistory @seeBuy="changeLatestBuy" :bonusBuyHistory="bonusBuyHistory" :currency="currency"/>
      </transition>
    </template>
  </div>
</template>
<script>
import axios from "axios";
import SvgCalendar from "/public/storage/assets/images/calendar.svg";
import SvgGame from "/public/storage/assets/images/slots.svg";
import SvgMoney from "/public/storage/assets/images/money.svg";
import SvgStakeSite from "/public/storage/assets/images/stake-site.svg";
import SvgBackward from "/public/storage/assets/images/backward.svg";
import BuyHistory from "./Components/BuyHistory.vue";
import LatestBuy from "./Components/LatestBuy.vue";

export default {
  components: {
    LatestBuy,
    BuyHistory,
    SvgCalendar,
    SvgGame,
    SvgMoney,
    SvgBackward,
    SvgStakeSite
  },
  data() {
    return {
      loading: true,
      streamer: {},
      bonusBuyHistory: [],
      latestBuy: null,
      refreshInterval: null,
      currency: 'RON',
      activeIndex: 0,
      bannerSpot: []
    };
  },
  props: {
    streamerId: {
      type: Number,
      required: true
    }
  },
  async mounted() {
    await this.getData();
    this.startAutoScroll();
  },
  methods: {
    async getData() {
      await this.getBonusBuyHistory();
      await this.getSettings();
      await this.getBannerAds();
    },
    async getBonusBuyHistory() {
      try {
        const response = await axios.get("/api/viewer/get-bbl-history/" + this.streamerId);
        this.bonusBuyHistory = response.data.bonusBuys;
        this.latestBuy = this.bonusBuyHistory[0]
      } catch (error) {
        console.error("Error fetching bonus buy history:", error);
      }
    },
    async getSettings() {
      let settingName = "obs_bonus_list";
      try {
        const response = await axios.get(`/api/get-setting-public`, {
          params: {setting_name: settingName, user_id: this.streamerId},
        });
        if (response.data.setting_value) {
          this.currency = JSON.parse(response.data.setting_value).currency;
        }
      } catch (error) {
        console.error(`Error loading setting "${settingName}":`, error);
      }
    },
    async getBannerAds() {
      try {
        this.loading = true;
        const response = await axios.get(`/api/banners-ads/` + this.streamerId);
        if (response.data.bannersAds) {
          this.bannerSpot = response.data.bannersAds;
        }
      } catch (error) {
        console.error(`Error loading setting "${settingName}":`, error);
      } finally {
        this.loading = false;
      }
    },
    async registerClick(bannerId) {
      try {
        await axios.post(`/api/banner-ads/${bannerId}/click`);
      } catch (error) {
        console.error("Error registering click:", error);
      }
    },
    changeLatestBuy(bonusBuy) {
      this.latestBuy = bonusBuy;
    },
    convertDate(isoString) {
      const date = new Date(isoString);
      return new Intl.DateTimeFormat("ro-RO", {
        day: "2-digit",
        month: "short",
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