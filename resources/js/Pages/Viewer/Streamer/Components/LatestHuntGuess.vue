<template>
  <div v-if="winners" class="backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg gap-4 flex flex-col text-white relative">
    <div class="text-lg font-semibold text-center mb-2">🏆 Câștigători 🏆</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div v-for="(winner, key) in winners" :key="key"
           class="flex items-center gap-3 bg-gray-900/80 p-4 rounded-lg shadow-lg border border-gray-700">
        <img v-if="winner?.profile_photo_path" :src="winner.profile_photo_path" alt="User Avatar" class="w-10 h-10 rounded-full border border-gray-600">
        <div v-else class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white text-xs">
          🏅
        </div>
        <!-- Winner Details -->
        <div class="flex flex-col">
          <div class="text-gray-300 text-xs uppercase">{{ winnerTitles[key] }}</div>
          <div class="font-semibold text-white">{{ winner ? (winner.yt_name || winner.name || "N/A") : "N/A" }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="relative" v-if="latestHunt">
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

      <div class="font-semibold">Predicții Hunt</div>

      <!-- Multiplier Inputs (Grouped) -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1">
          <label for="highestMulti" class="text-gray-300">Cel mai mare multi</label>
          <input
              id="highestMulti"
              v-model.number="highestMulti"
              type="number"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-indigo-500"
          />
        </div>
        <div class="flex flex-col gap-1">
          <label for="lowestMulti" class="text-gray-300">Cel mai mic multi</label>
          <input
              id="lowestMulti"
              v-model.number="lowestMulti"
              type="number"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-indigo-500"
          />
        </div>
      </div>

      <!-- Best & Worst Game (Grouped) -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1">
          <label for="bestGame" class="text-gray-300">Cel mai bun joc</label>
          <select
              id="bestGame"
              v-model="bestGame"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-green-500"
          >
            <option v-for="game in latestHunt.bonus_hunt_games" :key="game.id" :value="game.id">
              {{ game.game.name }}
            </option>
          </select>
        </div>

        <div class="flex flex-col gap-1">
          <label for="worstGame" class="text-gray-300">Cel mai prost joc</label>
          <select
              id="worstGame"
              v-model="worstGame"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-red-500"
          >
            <option v-for="game in latestHunt.bonus_hunt_games" :key="game.id" :value="game.id">
              {{ game.game.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Total Payout + Submit Button -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1">
          <label for="totalPayout" class="text-gray-300">Cât plătește lista</label>
          <input
              id="totalPayout"
              v-model.number="totalPayout"
              type="number"
              class="bg-gray-900 text-white border border-gray-700 rounded p-2 outline-none focus:ring focus:ring-indigo-500"
          />
        </div>

        <div class="flex flex-col justify-end">
          <button
              @click="submitPredictions"
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300"
              :disabled="!latestHunt.is_open"
          >
            {{ existingPrediction ? "Update Predicții" : "Trimite Predicții" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Overlay when hunt is closed -->
    <div
        v-if="!latestHunt.is_open"
        class="absolute inset-0 bg-black/30 backdrop-blur-md flex items-center justify-center rounded-lg p-0"
    >
      <div class=" p-1 rounded-lg text-white">

        <div class="flex flex-col text-xs" v-if="!notPredicted">
          <div class="text-sm font-semibold text-left">Predicțiile tale</div>
          <div class="w-full mt-2 border border-gray-700 rounded-lg flex flex-row">
            <div class="flex flex-col">
              <div class="border-b border-gray-700 flex flex-row">
                <div class="p-2">Cel mai mare multi</div>
                <div class="p-2">{{ highestMulti }}x</div>
              </div>
              <div class="border-b border-gray-700 flex flex-row">
                <div class="p-2">Cel mai mic multi</div>
                <div class="p-2">{{ lowestMulti }}x</div>
              </div>
            </div>
            <div class="flex flex-col">
              <div class="border-b border-gray-700 flex flex-row">
                <div class="p-2">Cel mai bun joc</div>
                <div class="p-2">{{ getGameName(bestGame) }}</div>
              </div>
              <div class="border-b border-gray-700 flex flex-row">
                <div class="p-2">Cel mai prost joc</div>
                <div class="p-2">{{ getGameName(worstGame) }}</div>
              </div>
            </div>
            <div class="border-b border-gray-700 flex flex-col">
              <div class="p-2">Cât plătește lista</div>
              <div class="p-2">{{ totalPayout }} {{currency}}</div>
            </div>
          </div>
        </div>
        <div v-else class="text-sm font-semibold text-left">Nu ai predicții pentru acest hunt</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    latestHunt: {
      type: Object,
      required: true
    },
    listType: {
      type: String,
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
      highestMulti: 0,
      lowestMulti: 0,
      totalPayout: 0,
      bestGame: "",
      worstGame: "",
      existingPrediction: null,
      notification: null,
      winners: null,
      winnerTitles: {
        resultWinner: "Cel mai Apropiat Rezultat",
        biggestMultiplierWinner: "Cel mai Mare Multiplicator",
        lowestMultiplierWinner: "Cel mai Mic Multiplicator",
        exactBiggestMultiplierGame: "Cel mai bun joc",
        exactLowestMultiplierGame: "Cel mai prost joc",
      },
      notPredicted: false
    };
  },
  async mounted() {
    await this.fetchExistingPrediction();
    if (!this.latestHunt.is_open) {
      await this.fetchWinners();
    }
  },
  methods: {
    async fetchExistingPrediction() {
      try {
        const response = await axios.get(`/api/viewer/get-prediction/${this.latestHunt.id}`);
        if (response.data && response.data.prediction) {
          this.notPredicted = false;
          this.existingPrediction = response.data.prediction;
          this.highestMulti = response.data.prediction.highestMulti;
          this.lowestMulti = response.data.prediction.lowestMulti;
          this.totalPayout = response.data.prediction.totalPayout;
          this.bestGame = response.data.prediction.bestGame;
          this.worstGame = response.data.prediction.worstGame;
        } else if(!this.latestHunt.is_open) {
          this.notPredicted = true;
        }
      } catch (error) {
        console.error("Error fetching existing prediction:", error);
      }
    },

    async submitPredictions() {
      if (!this.latestHunt.is_open) return;

      const payload = {
        highestMulti: this.highestMulti,
        lowestMulti: this.lowestMulti,
        totalPayout: this.totalPayout,
        bestGame: this.bestGame,
        worstGame: this.worstGame,
        bonusId: this.latestHunt.id,
        listType: this.listType
      };

      try {
        const response = await axios.post("/api/viewer/add-prediction", payload);
        this.notification = response.data.message;
        await this.fetchExistingPrediction(); // Refresh prediction after submission
        setTimeout(() => (this.notification = null), 3000); // Hide notification after 3s
      } catch (error) {
        console.error("Error submitting predictions:", error);
        this.notification = "Eroare la trimiterea predicției!";
        setTimeout(() => (this.notification = null), 3000);
      }
    },
    async fetchWinners() {
      try {
        const response = await axios.get(`/api/get-bonus-winner/${this.latestHunt.user_id}`);
        this.winners = response.data.winners;
      } catch (error) {
        console.error("No winners found.");
      }
    },
    getGameName(gameId) {
      const game = this.latestHunt.bonus_hunt_games.find(g => g.id === gameId);
      return game ? game.game.name : "Necunoscut";
    }
  }
};
</script>
