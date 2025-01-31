<template>
  <div class="p-6 rounded-lg shadow-md bg-gray-100 dark:bg-gray-800">
    <!-- Bonus Hunt Info -->
    <div class="space-y-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input
            :disabled="isEnded"
            :class="{'input-disabled': isEnded}"
            type="text"
            v-model="bonusHunt.name"
            class="input-primary"
            placeholder="Nume Lista"
        />
        <input
            :disabled="isEnded"
            :class="{'input-disabled': isEnded}"
            @input="debounceUpdateBonusHunt"
            type="number"
            v-model="bonusHunt.start"
            class="input-primary"
            placeholder="Cost (LEI)"
        />
        <input
            disabled
            type="number"
            v-model="bonusHunt.result"
            class="input-disabled"
            placeholder="Rezultat (LEI)"
        />
      </div>
      <button @click="wantToReset" class="btn-primary w-full md:w-auto">
        LISTA NOUA
      </button>
    </div>

    <!-- Bonus Hunt Games -->
    <template v-if="bonusHuntGames.length > 0">
      <div class="hidden md:grid grid-cols-[10px_50px_1fr_120px_120px_120px_50px] gap-4 text-sm font-semibold text-gray-600 dark:text-gray-300">
        <span>#</span>
        <span></span>
        <span>Joc</span>
        <span>Miză</span>
        <span>Rezultat (LEI)</span>
        <span>Multiplicator</span>
        <span></span>
      </div>

      <div
          v-for="(game, index) in bonusHuntGames"
          :key="game.id || index"
          class="grid grid-cols-1 md:grid-cols-[10px_50px_1fr_120px_120px_120px_50px] gap-4 items-center mb-4 p-2 rounded-lg shadow bg-white dark:bg-gray-700"
      >
        <div class="text-center text-gray-800 dark:text-gray-200 font-medium">
          {{ index + 1 }}
        </div>
        <template v-if="game.game_id">
          <img
              :src="getGameThumbnail(game.game_id)"
              alt="Game Thumbnail"
              class="w-20 h-20 object-cover rounded-lg mb-2"
          />
        </template>
        <span v-else></span>
        <v-select
            :disabled="isEnded || loading"
            :class="{'input-disabled': isEnded || loading}"
            :options="gameOptions"
            label="name"
            :reduce="game => game.id"
            @update:modelValue="value => debounceFieldUpdate(game, 'game_id')"
            v-model="game.game_id"
        />
        <input
            :disabled="isEnded || loading"
            :class="{'input-disabled': isEnded || loading}"
            @input="debounceFieldUpdate(game, 'stake')"
            v-model="game.stake"
            min="1"
            type="number"
            class="input-primary text-center"
            placeholder="Miză"
        />
        <input
            :disabled="isEnded || loading"
            :class="{'input-disabled': isEnded || loading}"
            @input="debounceFieldUpdate(game, 'result')"
            v-model="game.result"
            type="number"
            step="0.1"
            class="input-primary text-center"
            placeholder="Rezultat (LEI)"
        />
        <input
            disabled
            v-model="game.multiplier"
            type="number"
            class="input-disabled text-center"
            placeholder="Multiplicator"
        />
        <button v-if="!isEnded" @click="removeBonusHuntGameRow(game.id)" class="btn-danger">
          ✕
        </button>
      </div>
    </template>

    <!-- Add Game Button -->
    <div class="flex justify-center mt-8" v-if="!isEnded">
      <button @click="createNewBonusHuntGameRow" class="btn-primary w-full max-w-md">
        + Adaugă Joc
      </button>
    </div>
  </div>
</template>

<script>
import { useGameStore } from "@/stores/gameStore";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

export default {
  components: { vSelect },
  data() {
    return {
      bonusHunt: [],
      bonusHuntGames: [],
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
      return Boolean(this.bonusHunt.ended);
    }
  },
  mounted() {
    this.getLatestList();
  },
  methods: {
    validateBonusHuntStart() {
      if (!this.bonusHunt.start || this.bonusHunt.start <= 0) {
        alert("Vă rugăm să introduceți costul listei (Costul Hunt-ului).");
        return false;
      }
      return true;
    },
    debounceUpdateBonusHunt() {
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer);
      }
      this.debounceTimer = setTimeout(() => {
        this.updateBonusHunt();
      }, 1000);
    },
    debounceFieldUpdate(game, field) {
      if (field === 'stake' || field === 'result') {
        this.calcCurentRowMultiplier(game);
      }
      if (!this.fieldUpdateTimeouts[game.id]) {
        this.fieldUpdateTimeouts[game.id] = {};
      }
      if (this.fieldUpdateTimeouts[game.id][field]) {
        clearTimeout(this.fieldUpdateTimeouts[game.id][field]);
      }
      this.fieldUpdateTimeouts[game.id][field] = setTimeout(() => {
        this.updateBonusHuntGame();
      }, 1000); // Adjust debounce delay as needed
    },
    getGameThumbnail(gameId) {
      const selectedGame = this.gameOptions.find(game => game.id === gameId);
      return selectedGame ? `/storage/games/${selectedGame.image}` : ''; // Return thumbnail URL or an empty string
    },
    async getLatestList() {
      try {
        const response = await axios.get("/api/bonus-hunt");
        this.bonusHunt = response.data.bonusHunt;
        this.bonusHuntGames = response.data.bonusHuntGames;
      } catch (error) {
        console.error(error);
      }
    },
    async updateBonusHunt() {
      let bonusHunt = this.bonusHunt;
      this.loading = true
      await axios
          .patch("/api/bonus-hunt", {
            bonusHunt,
          })
          .catch(function (error) {
            console.log(error);
          }).finally(
              async () => {
                await this.getLatestList()
                this.loading = false
              }
          );
    },
    async updateBonusHuntGame() {
      if (!this.validateBonusHuntStart()) {
        return;
      }
      try {
        this.loading = true;
        let games = this.bonusHuntGames;
        await axios.put(`/api/bonus-hunt-games`, {games});
      } catch (error) {
        console.error(error);
      } finally {
        await this.getLatestList();
        this.loading = false;
      }
    },
    async createNewBonusHuntGameRow() {
      if (!this.validateBonusHuntStart()) {
        return;
      }
      try {
        await axios.post("/api/bonus-hunt-games", {
          bonusHuntId: this.bonusHunt.id,
        });
        await this.getLatestList();
      } catch (error) {
        console.error(error);
      }
    },
    async removeBonusHuntGameRow(gameId) {
      try {
        await axios.delete(`/api/bonus-hunt-games/${gameId}`);
        await this.getLatestList();
      } catch (error) {
        console.error(error);
      }
    },
    wantToReset() {
      if (!this.validateBonusHuntStart()) {
        return;
      }
      this.$dialog({
        message: "Ești sigur că vrei să faci o listă nouă?",
        buttons: ["da", "nu"],
        da: this.resetBonusHunt,
        nu: () => console.log("Canceled reset"),
      });
    },
    async resetBonusHunt() {
      try {
        await axios.post("/api/bonus-hunt");
        await this.getLatestList();
        this.$emit("newlist");
      } catch (error) {
        console.error(error);
      }
    },
    calcCurentRowMultiplier(game) {
      const gameIndex = this.bonusHuntGames.findIndex(g => g.id === game.id);
      if (gameIndex === -1) {
        console.error("Game not found in bonusHuntGames.");
        return;
      }
      const stake = parseFloat(game.stake);
      const result = parseFloat(game.result);
      if (!game.game_id || isNaN(stake) || isNaN(result) || result < 0) {
        console.warn("Invalid game data: Missing or invalid stake or result.");
        return;
      }
      const multiplier = result === 0 ? 0 : Math.round(result / stake);
      this.bonusHuntGames[gameIndex] = {
        ...game,
        multiplier,
        result,
        stake,
      };
    },
  },
};
</script>
