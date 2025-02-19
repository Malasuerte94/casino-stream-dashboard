<template>
  <div class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg gap-2 flex flex-col">
    <div>
      <div class="font-semibold text-white flex flex-row gap-1 items-center">
        <div><SvgUp class="h-5 w-5 text-white"/></div>
        <div>Cel mai mare multi</div>
      </div>
      <div v-if="highestMultiGame" class="flex flex-row justify-between text-gray-300">
        <div>{{ highestMultiGame.game.name }}</div>
        <div class="text-green-400">{{ highestMultiGame.multiplier }}x</div>
      </div>
      <div v-else class="text-gray-500 italic">Nu sunt date suficiente</div>
    </div>
    <div>
      <div class="font-semibold text-white flex flex-row gap-1 items-center">
        <div><SvgDown class="h-5 w-5 text-white"/></div>
        <div>Cel mai mic multi</div>
      </div>
      <div v-if="lowestMultiGame" class="flex flex-row justify-between text-gray-300">
        <div>{{ lowestMultiGame.game.name }}</div>
        <div class="text-red-400">{{ lowestMultiGame.multiplier }}x</div>
      </div>
      <div v-else class="text-gray-500 italic">Nu sunt date suficiente</div>
    </div>
  </div>
</template>

<script>
import SvgUp from "/public/storage/assets/images/up.svg";
import SvgDown from "/public/storage/assets/images/down.svg";

export default {
  components: {
    SvgUp,
    SvgDown
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
    highestMultiGame() {
      if (!this.latestHunt?.bonus_hunt_games?.length) return null;
      return this.latestHunt.bonus_hunt_games.reduce((max, game) =>
              Number(game.multiplier) > Number(max.multiplier) ? game : max
          , this.latestHunt.bonus_hunt_games[0]);
    },
    lowestMultiGame() {
      if (!this.latestHunt?.bonus_hunt_games?.length) return null;
      return this.latestHunt.bonus_hunt_games.reduce((min, game) =>
              Number(game.multiplier) < Number(min.multiplier) ? game : min
          , this.latestHunt.bonus_hunt_games[0]);
    }
  }
};
</script>
