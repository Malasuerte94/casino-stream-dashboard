<template>
  <div class="flex flex-col md:flex-row gap-4 w-full mb-4" v-if="latestBuy">
    <!-- Games Table -->
    <div class="md:w-1/2 w-full">
      <table v-if="latestBuy.bonus_buy_games.length > 0"
             class="w-full text-white rounded-lg overflow-hidden">
        <thead>
        <tr class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 text-left">
          <th class="px-2 py-1 md:px-4 md:py-3">
              <span class="flex items-center gap-2">
                <SvgGame class="w-4 h-4 text-indigo-500" /> Joc
              </span>
          </th>
          <th class="px-2 py-1 md:px-4 md:py-3">
              <span class="flex items-center gap-2">
                <SvgStake class="w-4 h-4 text-indigo-500" /> Bet
              </span>
          </th>
          <th class="px-2 py-1 md:px-4 md:py-3">
              <span class="flex items-center gap-2">
                <SvgStake class="w-4 h-4 text-indigo-500" /> Cost
              </span>
          </th>
          <th class="px-2 py-1 md:px-4 md:py-3">
              <span class="flex items-center gap-2">
                <SvgMoney class="w-4 h-4 text-indigo-500" /> Plata
              </span>
          </th>
          <th class="px-2 py-1 md:px-4 md:py-3">
              <span class="flex items-center gap-2">
                <SvgMultiplier class="w-4 h-4 text-indigo-500" /> Multi
              </span>
          </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(game, index) in latestBuy.bonus_buy_games"
            :key="game.id"
            class="backdrop-blur-xl shadow-lg shadow-black/40"
            :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
          <template v-if="game.game && game.game.name">
            <td class="px-2 py-1 md:px-4 md:py-3 flex items-center gap-2">{{ game.game.name }}</td>
            <td class="px-2 py-1 md:px-4 md:py-3 text-left">{{ game.stake }}</td>
            <td class="px-2 py-1 md:px-4 md:py-3 text-left">{{ formatCurrency(game.price) }}</td>
            <td class="px-2 py-1 md:px-4 md:py-3 text-left">{{ formatCurrency(game.result) }}</td>
            <td class="px-2 py-1 md:px-4 md:py-3 text-left">{{ game.multiplier }}x</td>
          </template>
        </tr>
        </tbody>
      </table>
      <div v-else class="text-gray-400 text-center">No games available</div>
    </div>
    <div class="md:w-1/2 w-full flex flex-col gap-4 text-white rounded-lg">
      <LatestBuyGuess :latestBuy="latestBuy" listType="buy" :currency="currency" />
      <LatestBuyGuessers :latestBuy="latestBuy" listType="buy" :currency="currency" />
      <div class="flex flex-col md:flex-row w-full gap-4">
        <div class="w-full md:w-1/2">
          <LatestBuyStats :latestBuy="latestBuy" :currency="currency" />
        </div>
        <div class="w-full md:w-1/2 flex flex-col gap-4">
          <LatestBuyCurentNext :latestBuy="latestBuy" :currency="currency"/>
          <LatestBuyHighLowX  :latestBuy="latestBuy" :currency="currency"/>
          <LatestBuyHighLowWin :latestBuy="latestBuy" :currency="currency"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SvgCalendar from "/public/storage/assets/images/calendar.svg";
import SvgGame from "/public/storage/assets/images/slots.svg";
import SvgMoney from "/public/storage/assets/images/money.svg";
import SvgBackward from "/public/storage/assets/images/backward.svg";
import SvgStake from "/public/storage/assets/images/stake.svg";
import SvgMultiplier from "/public/storage/assets/images/multiplier.svg";
import LatestBuyStats from "./LatestBuyStats.vue";
import LatestBuyCurentNext from "./LatestBuyCurentNext.vue";
import LatestBuyHighLowX from "./LatestBuyHighLowX.vue";
import LatestBuyHighLowWin from "./LatestBuyHighLowWin.vue";
import LatestBuyGuess from "./LatestBuyGuess.vue";
import axios from "axios";
import LatestBuyGuessers from "./LatestBuyGuessers.vue";
export default {
  components: {
    LatestBuyGuessers,
    LatestBuyGuess,
    LatestBuyHighLowWin,
    LatestBuyHighLowX,
    LatestBuyCurentNext,
    LatestBuyStats,
    SvgCalendar,
    SvgGame,
    SvgMoney,
    SvgBackward,
    SvgStake,
    SvgMultiplier
  },
  props: {
    buyId: {
      type: Number,
      required: true
    },
    currency: {
      type: String,
      required: false,
      default: 'RON'
    }
  },
  data() {
    return {
      latestBuy: null,
    };
  },
  mounted() {
    this.getBonusBuy();
    this.refreshInterval = setInterval(() => {
      this.getBonusBuy();
    }, 5000);
  },
  beforeUnmount() {
    clearInterval(this.refreshInterval);
  },
  methods: {
    async getBonusBuy() {
      try {
        const response = await axios.get("/api/viewer/get-bbl/" + this.buyId);
        this.latestBuy = response.data.latestBuy;
      } catch (error) {
        console.error("Error fetching bonus buy history:", error);
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("ro-RO", {
        style: "currency",
        currency: this.currency,
        minimumFractionDigits: 0
      }).format(value);
    }
  },
  watch: {
    buyId(newBuyId) {
      this.getBonusBuy();
    }
  }
};
</script>
