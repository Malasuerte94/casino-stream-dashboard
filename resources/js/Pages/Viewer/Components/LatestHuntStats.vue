<template>
  <div class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg">
    <div class="flex flex-col gap-4">
      <!-- Header -->
      <div class="flex flex-row gap-2 items-center text-white">
        <SvgTarget class="h-5 w-5 text-yellow-400" />
        <span class="font-semibold">Hunt Stats</span>
      </div>

      <!-- Stats List -->
      <div class="grid grid-cols-1 gap-3 text-white">
        <div class="flex justify-between flex-col">
          <span>Jocuri</span>
          <span class="font-bold">{{ totalBonuses }}</span>
        </div>
        <div class="flex justify-between flex-col">
          <span>Jocuri Deschise</span>
          <span class="font-bold">{{ openedBonuses }}</span>
        </div>
        <div class="flex justify-between flex-col">
          <span>Câștig Curent</span>
          <span class="font-bold text-green-400">{{ formatCurrency(currentTotalWin) }}</span>
        </div>
        <div class="flex justify-between flex-col">
          <span>Break Even X</span>
          <span class="font-bold text-blue-400">{{ breakEvenX }}x</span>
        </div>
        <div class="flex justify-between flex-col">
          <span>Multi Curent X</span>
          <span class="font-bold text-purple-400">{{ currentX }}x</span>
        </div>
        <div class="flex justify-between flex-col">
          <span>Cost Listă</span>
          <span class="font-bold text-orange-400">{{ formatCurrency(startBalance) }}</span>
        </div>
        <div class="flex justify-between flex-col">
          <span>AVG. Bet</span>
          <span class="font-bold text-yellow-400">{{ formatCurrency(avgBet) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SvgTarget from "/public/storage/assets/images/target.svg";

export default {
  components: {
    SvgTarget
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
  computed: {
    totalBonuses() {
      return this.latestHunt?.bonus_hunt_games.length || 0;
    },
    openedBonuses() {
      return this.latestHunt?.bonus_hunt_games.filter(game => Number(game.result)).length || 0;
    },
    currentTotalWin() {
      return this.latestHunt?.bonus_hunt_games.reduce((sum, game) => sum + Number(game.result) || 0, 0);
    },
    breakEvenX() {
      const totalBets = this.latestHunt?.bonus_hunt_games.reduce((sum, game) => sum + (Number(game.stake) || 0), 0);
      return totalBets > 0 ? (this.startBalance / totalBets).toFixed(2) : 0;
    },
    currentX() {
      const totalBets = this.latestHunt?.bonus_hunt_games.reduce((sum, game) => sum + (Number(game.stake) || 0), 0);
      return totalBets > 0 ? (this.currentTotalWin / totalBets).toFixed(2) : 0;
    },
    startBalance() {
      return this.latestHunt?.start || 0;
    },
    avgBet() {
      return this.totalBonuses > 0
          ? (this.latestHunt?.bonus_hunt_games.reduce((sum, game) => sum + (Number(game.stake) || 0), 0) / this.totalBonuses).toFixed(2)
          : 0;
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
