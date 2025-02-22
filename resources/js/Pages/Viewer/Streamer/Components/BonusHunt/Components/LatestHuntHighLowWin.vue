<template>
  <div class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg gap-2 flex flex-col">
    <!-- Highest Win Game -->
    <div>
      <div class="font-semibold text-white flex flex-row gap-1 items-center">
        <div><SvgCrow class="h-4 w-4 text-white"/></div>
        <div>Cea mai mare plată</div>
      </div>
      <div v-if="highestWinGame" class="flex flex-row justify-between text-gray-300">
        <div>{{ highestWinGame.game.name }}</div>
        <div class="text-green-400">{{ formatCurrency(highestWinGame.result) }}</div>
      </div>
      <div v-else class="text-gray-500 italic">Nu sunt date suficiente</div>
    </div>

    <!-- Lowest Win Game -->
    <div>
      <div class="font-semibold text-white flex flex-row gap-1 items-center">
        <div><SvgShit class="h-4 w-4 text-white"/></div>
        <div>Cea mai mică plată</div>
      </div>
      <div v-if="lowestWinGame" class="flex flex-row justify-between text-gray-300">
        <div>{{ lowestWinGame.game.name }}</div>
        <div class="text-red-400">{{ formatCurrency(lowestWinGame.result) }}</div>
      </div>
      <div v-else class="text-gray-500 italic">Nu sunt date suficiente</div>
    </div>
  </div>
</template>

<script>
import SvgCrow from "/public/storage/assets/images/crown.svg";
import SvgShit from "/public/storage/assets/images/shit.svg";

export default {
  components: {
    SvgCrow,
    SvgShit
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
    highestWinGame() {
      if (!this.latestHunt?.bonus_hunt_games?.length) return null;
      return this.latestHunt.bonus_hunt_games.reduce((max, game) =>
              Number(game.result) > Number(max.result) ? game : max
          , this.latestHunt.bonus_hunt_games[0]);
    },
    lowestWinGame() {
      if (!this.latestHunt?.bonus_hunt_games?.length) return null;
      return this.latestHunt.bonus_hunt_games.reduce((min, game) =>
              Number(game.result) < Number(min.result) ? game : min
          , this.latestHunt.bonus_hunt_games[0]);
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
