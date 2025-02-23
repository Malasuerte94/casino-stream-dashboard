<template>
  <div v-if="winners" class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg gap-4 flex flex-col text-white relative">
    <div class="text-lg font-semibold text-center mb-2">🏆 Câștigători 🏆</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div v-for="(winner, key) in winners" :key="key"
           class="flex items-center gap-3 bg-gray-900/80 p-4 rounded-lg shadow-lg border border-gray-700">
        <img v-if="winner?.user?.profile_photo_path" :src="winner.user.profile_photo_path" alt="User Avatar"
             class="w-10 h-10 rounded-full border border-gray-600">
        <div v-else class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white text-xs">
          🏅
        </div>
        <div class="flex flex-col">
          <div class="text-gray-300 text-xs uppercase">{{ winnerTitles[key] }}</div>
          <div class="font-semibold text-white">{{ winner?.user?.yt_name || winner?.user?.name || "N/A" }}</div>
          <div v-if="key === 'resultWinner'">
            <span>{{ formatCurrency(winner.estimated) }}</span>
            <span :class="getDifferenceClass(winner.estimated, battleResult.result)">
              ({{ formatDifference(winner.estimated, battleResult.result) }})
            </span>
          </div>
          <div v-else-if="key === 'biggestMultiplierWinner' || key === 'lowestMultiplierWinner'">
            <span>{{ formatMultiplier(winner.estimated) }}</span>
            <span :class="getDifferenceClass(winner.estimated, battleResult[key.replace('Winner', '')])">
              ({{ formatMultiplierDifference(winner.estimated, battleResult[key.replace('Winner', '')]) }})
            </span>
          </div>
          <div v-else-if="key === 'exactBiggestMultiplierGame' || key === 'exactLowestMultiplierGame'">
            <span>{{ getGameName(winner?.game_id) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="relative" v-if="latestBattle?.battle">
    <!-- Main Form -->
    <div
        class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg gap-4 flex flex-col text-white relative"
    >
      <!-- Notification -->
      <transition name="fade">
        <div v-if="notification" class="absolute top-0 left-0 right-0 bg-green-600 text-white p-2 rounded-t-lg text-center">
          {{ notification }}
        </div>
      </transition>

      <div class="font-semibold">Predicții Turneu</div>

      <!-- Multiplier Inputs (Grouped) -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1">
          <label for="highestMulti" class="text-gray-300">Jocul cu cea mai mare plată (x)</label>
          <select
              id="bestGame"
              v-model="gameHighestMulti"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-green-500"
          >
            <option v-for="game in latestBattle.participants" :key="game.id" :value="game.id">
              {{ game.game.name }}
            </option>
          </select>
        </div>
        <div class="flex flex-col gap-1">
          <label for="lowestMulti" class="text-gray-300">Jocul cu cea mai mică plată (x)</label>
          <select
              id="bestGame"
              v-model="gameLowestMulti"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-green-500"
          >
            <option v-for="game in latestBattle.participants" :key="game.id" :value="game.id">
              {{ game.game.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Best & Worst Game (Grouped) -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1">
          <label for="bestGame" class="text-gray-300">Câștigătorul</label>
          <select
              id="bestGame"
              v-model="gameWinner"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-green-500"
          >
            <option v-for="game in latestBattle.participants" :key="game.id" :value="game.id">
              {{ game.game.name }}
            </option>
          </select>
        </div>
        <div class="flex flex-col justify-end">
          <button
              @click="submitPredictions"
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300"
              :disabled="!latestBattle.battle.is_open"
          >
            {{ existingPrediction ? "Update Predicții" : "Trimite Predicții" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Overlay when hunt is closed -->
    <div
        v-if="!$page.props.user.id || !latestBattle.battle.is_open"
        class="absolute inset-0 bg-black/30 backdrop-blur-md flex items-center justify-center rounded-lg p-0"
    >
      <div class=" p-1 rounded-lg text-white">
        <div class="flex flex-col text-xs" v-if="!notPredicted">
          <div class="text-sm font-semibold text-left">Predicțiile tale</div>
          <div class="w-full mt-2 border border-gray-700 rounded-lg flex flex-row">
            <div class="flex flex-col">
              <div class="border-b border-gray-700 flex flex-row">
                <div class="p-2">Jocul care plătește cel mai mult</div>
                <div class="p-2">{{ getGameName(gameLowestMulti) }}</div>
              </div>
              <div class="border-b border-gray-700 flex flex-row">
                <div class="p-2">Jocul care plătește cel mai puțin</div>
                <div class="p-2">{{ getGameName(gameHighestMulti) }}</div>
              </div>
              <div class="border-b border-gray-700 flex flex-row">
                <div class="p-2">Câștigătorul Turneului</div>
                <div class="p-2">{{ getGameName(gameWinner) }}</div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-sm font-semibold text-left">Nu ai predicții pentru acest battle</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    latestBattle: {
      type: Object,
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
      gameWinner: 0,
      gameLowestMulti: 0,
      gameHighestMulti: 0,
      existingPrediction: null,
      notification: null,
      winners: null,
      battleResult: null,
      notPredicted: false
    };
  },
  async mounted() {
    await this.fetchExistingPrediction();
    if (!this.latestBattle.battle.is_open) {
      await this.fetchWinners();
    }
  },
  methods: {
    async fetchExistingPrediction() {
      try {
        const response = await axios.get(`/api/viewer/get-bb-prediction/${this.latestBattle.battle.id}`);
        if (response.data && response.data.prediction) {
          this.notPredicted = false;
          this.existingPrediction = response.data.prediction;
          this.gameWinner = response.data.prediction.gameWinner;
          this.gameLowestMulti = response.data.prediction.gameLowestMulti;
          this.gameHighestMulti = response.data.prediction.gameHighestMulti;
        } else if(!this.latestBattle.battle.is_open) {
          this.notPredicted = true;
        }
      } catch (error) {
        if(!this.$page.props.user.id) {
          this.notPredicted = true;
        }
        console.error("Error fetching existing prediction:", error);
      }
    },
    async submitPredictions() {
      if (!this.latestBattle.battle.is_open) return;
      const payload = {
        gameWinner: this.gameWinner,
        gameLowestMulti: this.gameLowestMulti,
        gameHighestMulti: this.gameHighestMulti,
        battleId: this.latestBattle.battle.id,
      };
      try {
        const response = await axios.post("/api/viewer/add-bb-prediction", payload);
        this.notification = response.data.message;
        await this.fetchExistingPrediction();
        setTimeout(() => (this.notification = null), 3000);
      } catch (error) {
        console.error("Error submitting predictions:", error);
        this.notification = "Eroare la trimiterea predicției!";
        setTimeout(() => (this.notification = null), 3000);
      }
    },
    async fetchWinners() {
      try {
        const response = await axios.get(`/api/get-bonus-winner/${this.latestBattle.battle.id}`);
        if(response.data && response.data.winners) {
          this.winners = response.data.winners;
          this.battleResult = response.data.results;
        } else {
          this.winners = null;
          this.battleResult = null;
        }
      } catch (error) {
        console.error("No winners found.");
      }
    },
    getGameName(gameId) {
      const game = this.latestBattle.participants.find(g => g.id === gameId);
      return game ? game.game.name : "Necunoscut";
    },
    formatCurrency(value) {
      return `${Number(value).toLocaleString()} ${this.currency}`;
    },
    formatMultiplier(value) {
      return `${Number(value)}x`;
    },
    formatMultiplierDifference(estimated, actual) {
      const diff = actual - estimated;
      return diff > 0 ? `+${diff}x` : `${diff}x`;
    },
    formatDifference(estimated, actual) {
      const diff = actual - estimated;
      return diff > 0 ? `+${diff.toLocaleString()} ${this.currency}` : `${diff.toLocaleString()} ${this.currency}`;
    },
    getDifferenceClass(estimated, actual) {
      return actual - estimated >= 0 ? 'text-green-400' : 'text-red-400';
    }
  },
  watch: {
    latestBattle() {
      this.fetchExistingPrediction();
      this.fetchWinners();
    }
  }
};
</script>
