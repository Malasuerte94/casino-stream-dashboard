<template>
  <div class="p-6 rounded-lg shadow-lg bg-gray-800">
    <!-- Bonus Buy Info -->
    <div class="space-y-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
            :disabled="isEnded"
            :class="{'input-disabled': isEnded}"
            type="text"
            v-model="bonusBuy.name"
            @input="debounceUpdateBonusBuy"
            class="input-primary"
            placeholder="Nume Lista"
        />
        <input
            type="number"
            v-model="bonusBuy.start"
            disabled
            readonly
            class="input-disabled"
            placeholder="Cost (LEI)"
        />
        <input
            type="number"
            v-model="bonusBuy.result"
            disabled
            readonly
            class="input-disabled"
            placeholder="Rezultat (LEI)"
        />
        <button @click="wantToReset" class="btn-primary w-full md:w-auto">
          LISTA NOUA
        </button>
      </div>
    </div>

    <!-- Bonus Buy Games -->
    <template v-if="bonusBuyGames.length > 0">
      <!-- Header Row -->
      <div class="hidden md:grid grid-cols-[10px_50px_1fr_120px_120px_120px_120px_50px] gap-4 text-sm font-semibold text-gray-300 mb-2">
        <span>#</span>
        <span></span>
        <span>Joc</span>
        <span>Miză</span>
        <span>Preț (LEI)</span>
        <span>Rezultat (LEI)</span>
        <span>Multiplicator</span>
        <span></span>
      </div>

      <!-- Game Rows with extra background div -->
      <div
          v-for="(game, index) in bonusBuyGames"
          :key="game.id || index"
          class="relative overflow-hidden rounded-lg shadow mb-4 game-row"
          :class="{'bg-gray-700': !game.game_id}"
      >
        <!-- Blurred Background Div -->
        <div
            v-if="game.game_id"
            class="bg-blur"
            :style="{ backgroundImage: 'url(' + getGameThumbnail(game.game_id) + ')' }"
        ></div>

        <!-- Row Content -->
        <div
            class="relative z-10 grid grid-cols-1 md:grid-cols-[10px_50px_1fr_120px_120px_120px_120px_50px] gap-4 items-center p-4"
            :class="game.game_id ? 'bg-transparent' : 'bg-white dark:bg-gray-700'"
        >
          <div class="text-center text-gray-200 font-medium">
            {{ index + 1 }}
          </div>
          <template v-if="game.game_id">
            <img
                :src="getGameThumbnail(game.game_id)"
                alt="Game Thumbnail"
                class="w-20 h-20 object-cover rounded-lg"
            />
          </template>
          <template v-else>
            <span></span>
          </template>
          <v-select
              :disabled="isEnded || loading"
              :class="{'input-disabled': isEnded || loading}"
              :options="gameOptions"
              label="name"
              :reduce="game => game.id"
              append-to-body
              @update:modelValue="value => debounceFieldUpdate(game, 'game_id')"
              v-model="game.game_id"
          />
          <input
              :disabled="isEnded || loading"
              :class="{'input-disabled': isEnded || loading}"
              type="number"
              v-model="game.stake"
              @input="debounceFieldUpdate(game, 'stake')"
              min="1"
              class="input-primary text-center"
              placeholder="Miză"
          />
          <input
              :disabled="isEnded || loading"
              :class="{'input-disabled': isEnded || loading}"
              type="number"
              v-model="game.price"
              @input="debounceFieldUpdate(game, 'price')"
              min="0"
              class="input-primary text-center"
              placeholder="Preț (LEI)"
          />
          <input
              :disabled="isEnded"
              :class="{'input-disabled': isEnded}"
              type="number"
              v-model="game.result"
              @input="debounceFieldUpdate(game, 'result')"
              step="0.1"
              class="input-primary text-center"
              placeholder="Rezultat (LEI)"
          />
          <input
              type="number"
              v-model="game.multiplier"
              disabled
              class="input-disabled text-center"
              placeholder="Multiplicator"
          />
          <button
              v-if="!isEnded"
              @click="removeBonusBuyGameRow(game.id)"
              class="btn-danger"
          >
            ✕
          </button>
        </div>
      </div>
    </template>

    <!-- Add Game Button -->
    <div class="flex justify-center mt-8" v-if="!isEnded">
      <button @click="createNewBonusBuyGameRow" class="btn-primary w-full max-w-md">
        + Game
      </button>
    </div>
  </div>
</template>

<script>
import { useGameStore } from "@/stores/gameStore";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import axios from "axios";

export default {
  components: { vSelect },
  data() {
    return {
      bonusBuy: {},
      bonusBuyGames: [],
      fieldUpdateTimeouts: {},
      debounceTimer: null,
      loading: false,
    };
  },
  computed: {
    gameOptions() {
      const gameStore = useGameStore();
      return gameStore.availableGames;
    },
    isEnded() {
      return Boolean(this.bonusBuy.ended);
    },
  },
  mounted() {
    this.getLatestList();
  },
  methods: {
    debounceUpdateBonusBuy() {
      if (this.debounceTimer) clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => this.updateBonusBuy(), 1000);
    },
    debounceFieldUpdate(game, field) {
      if (field === "result" || field === "price") {
        this.calcCurentRowMultiplier(game);
      }
      if (!this.fieldUpdateTimeouts[game.id]) {
        this.fieldUpdateTimeouts[game.id] = {};
      }
      if (this.fieldUpdateTimeouts[game.id][field]) {
        clearTimeout(this.fieldUpdateTimeouts[game.id][field]);
      }
      this.fieldUpdateTimeouts[game.id][field] = setTimeout(() => {
        this.updateBonusBuyGames();
      }, 1000);
    },
    getGameThumbnail(gameId) {
      const selectedGame = this.gameOptions.find((game) => game.id === gameId);
      const url = selectedGame ? `/storage/games/${selectedGame.image}` : "";
      console.log("Thumbnail URL:", url); // Debugging log
      return url;
    },
    async getLatestList() {
      try {
        const response = await axios.get("/api/bonus-buy");
        this.bonusBuy = response.data.bonusBuy;
        this.bonusBuyGames = response.data.bonusBuyGames;
      } catch (error) {
        console.error(error);
      }
    },
    async updateBonusBuy() {
      try {
        await axios.patch("/api/bonus-buy", { bonusBuy: this.bonusBuy });
        await this.getLatestList();
      } catch (error) {
        console.error(error);
      }
    },
    async updateBonusBuyGames() {
      try {
        this.loading = true;
        await axios.put("/api/bonus-buy-games", { games: this.bonusBuyGames });
      } catch (error) {
        console.error(error);
      } finally {
        await this.getLatestList();
        this.loading = false;
      }
    },
    async createNewBonusBuyGameRow() {
      try {
        await axios.post("/api/bonus-buy-games", { bonusBuyId: this.bonusBuy.id });
        await this.getLatestList();
      } catch (error) {
        console.error(error);
      }
    },
    async removeBonusBuyGameRow(gameId) {
      try {
        await axios.delete(`/api/bonus-buy-games/${gameId}`);
        await this.getLatestList();
      } catch (error) {
        console.error(error);
      }
    },
    wantToReset() {
      this.$dialog({
        message: "Ești sigur că vrei să faci o listă nouă?",
        buttons: ["da", "nu"],
        da: this.resetBonusBuy,
        nu: () => console.log("Canceled reset"),
      });
    },
    async resetBonusBuy() {
      try {
        await axios.post("/api/close-bonus-list", {
          close: true,
          list_id: this.bonusBuy.id,
          type: "buy",
        });
        await axios.post("/api/bonus-buy");
        await this.getLatestList();
        this.$emit("newlist");
      } catch (error) {
        console.error(error);
      }
    },
    calcCurentRowMultiplier(game) {
      if (!game.price || !game.result || game.result < 0) return;
      const multiplier = Math.round((game.result / game.price) * 100) / 100;
      game.multiplier = multiplier;
    },
  },
};
</script>

<style scoped>
/* Custom Classes for Dark Mode */

/* Primary input styling */
.input-primary {
  @apply bg-gray-900 border border-gray-600 text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition-colors duration-200;
}

/* Disabled input styling */
.input-disabled {
  @apply bg-gray-700 border border-gray-600 text-gray-500 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed;
}

/* Primary button styling */
.btn-primary {
  @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition;
}

/* Danger button styling */
.btn-danger {
  @apply bg-red-500 text-white font-semibold py-1 px-3 rounded-full hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-300 transition;
}

/* Blurred background div */
.bg-blur {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  filter: blur(8px);
  opacity: 0.3;
  pointer-events: none;
  z-index: 0;
  /* For debugging: remove or comment out after confirming it works */
  /* border: 1px solid red; */
}
</style>
