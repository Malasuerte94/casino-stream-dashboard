<template>
  <div class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg gap-2 flex flex-col">
    <!-- Current Playing Game -->
    <div>
      <div class="font-semibold text-white">Jocul Curent</div>
      <div v-if="currentGame && currentGame.game" class="flex flex-row justify-between text-gray-300">
        <div>{{ currentGame.game.name }}</div>
        <div>{{ formatCurrency(currentGame.result) }}</div>
      </div>
      <div v-else class="text-gray-500 italic">Niciun joc în desfășurare</div>
    </div>
    <div>
      <div class="font-semibold text-white flex flex-row gap-1 items-center">
        <div><SvgForward class="h-5 w-5 text-white stroke-current"/></div>
        <div> Următorul Joc</div>
      </div>
      <div v-if="nextGame && nextGame.game" class="flex flex-row justify-between text-gray-300">
        <div>{{ nextGame.game.name }}</div>
        <div>{{ formatCurrency(nextGame.result) }}</div>
      </div>
      <div v-else class="text-gray-500 italic">Niciun joc următor</div>
    </div>
  </div>
</template>

<script>
import SvgForward from "/public/storage/assets/images/forward.svg";
export default {
  components: {
    SvgForward
  },
  props: {
    latestBuy: {
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
    currentGame() {
      if (!this.latestBuy || !this.latestBuy.bonus_buy_games) return null;
      return this.latestBuy.bonus_buy_games.find(game => Number(game.result) === 0 && game.game) || null;
    },
    nextGame() {
      if (!this.latestBuy || !this.latestBuy.bonus_buy_games) return null;
      const index = this.latestBuy.bonus_buy_games.findIndex(game => Number(game.result) === 0 && game.game);
      return index !== -1 && index + 1 < this.latestBuy.bonus_buy_games.length
          ? this.latestBuy.bonus_buy_games[index + 1]
          : null;
    }
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat("ro-RO", {
        style: "currency",
        currency: this.currency,
        minimumFractionDigits: 2
      }).format(value);
    }
  }
};
</script>
