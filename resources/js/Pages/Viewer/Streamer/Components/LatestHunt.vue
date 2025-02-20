<template>
  <div class="flex flex-col md:flex-row gap-4 w-full mb-4">
    <!-- Games Table -->
    <div class="md:w-1/2 w-full">
      <table v-if="latestHunt && latestHunt.bonus_hunt_games.length > 0"
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
        <tr v-for="(game, index) in latestHunt.bonus_hunt_games"
            :key="game.id"
            class="backdrop-blur-xl shadow-lg shadow-black/40"
            :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
          <template v-if="game.game && game.game.name">
            <td class="px-2 py-1 md:px-4 md:py-3 flex items-center gap-2">{{ game.game.name }}</td>
            <td class="px-2 py-1 md:px-4 md:py-3 text-left">{{ game.stake }}</td>
            <td class="px-2 py-1 md:px-4 md:py-3 text-left">{{ formatCurrency(game.result) }}</td>
            <td class="px-2 py-1 md:px-4 md:py-3 text-left">{{ game.multiplier }}x</td>
          </template>
        </tr>
        </tbody>
      </table>
      <div v-else class="text-gray-400 text-center">No games available</div>
    </div>
    <!-- Hunt Stats -->
    <div class="md:w-1/2 w-full flex flex-col gap-4 text-white rounded-lg">
      <LatestHuntGuess :latestHunt="latestHunt" listType="hunt" :currency="currency" />
      <div class="flex flex-col md:flex-row w-full gap-4">
        <div class="w-full md:w-1/2">
          <LatestHuntStats :latestHunt="latestHunt" :currency="currency" />
        </div>
        <div class="w-full md:w-1/2 flex flex-col gap-4">
          <LatestHuntCurentNext :latestHunt="latestHunt" :currency="currency"/>
          <LatestHuntHighLowX  :latestHunt="latestHunt" :currency="currency"/>
          <LatestHuntHighLowWin :latestHunt="latestHunt" :currency="currency"/>
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
import LatestHuntStats from "./LatestHuntStats.vue";
import LatestHuntCurentNext from "./LatestHuntCurentNext.vue";
import LatestHuntHighLowX from "./LatestHuntHighLowX.vue";
import LatestHuntHighLowWin from "./LatestHuntHighLowWin.vue";
import LatestHuntGuess from "./LatestHuntGuess.vue";
export default {
  components: {
    LatestHuntGuess,
    LatestHuntHighLowWin,
    LatestHuntHighLowX,
    LatestHuntCurentNext,
    LatestHuntStats,
    SvgCalendar,
    SvgGame,
    SvgMoney,
    SvgBackward,
    SvgStake,
    SvgMultiplier
  },
  props: {
    latestHunt: {
      type: Object,
      required: true
    },
    currency: {
      type: String,
      required: false,
      default: 'RON'
    }
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat("ro-RO", {
        style: "currency",
        currency: this.currency,
        minimumFractionDigits: 0
      }).format(value);
    }
  }
};
</script>
