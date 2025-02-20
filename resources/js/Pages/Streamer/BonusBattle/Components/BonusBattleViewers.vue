<template>
  <div class="p-2 rounded-md shadow-md md:w-1/3 bg-gray-800 space-y-4 transition-all duration-300">
    <!-- Toggle Switch Section using Tailwind CSS -->
    <div class="flex items-center justify-between p-2 bg-gray-700 rounded">
      <label for="toggle" class="relative inline-flex items-center cursor-pointer">
        <input
            type="checkbox"
            id="toggle"
            v-model="setting"
            @change="updateSetting"
            class="sr-only peer"
            :disabled="loading"
        />
        <div
            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full
          dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white
          after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border
          after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"
        ></div>
        <span class="ml-3 text-sm font-medium text-gray-300">
          {{ setting ? 'Înscrieri Pornite prin Bot' : 'Înscrieri Oprite prin Bot' }}
          <span v-if="loading" class="ml-2 text-xs italic text-gray-400">Loading...</span>
        </span>
      </label>
    </div>

    <!-- Winners Table Section (if any winners have been picked) -->
    <div v-if="winners.length" class="overflow-auto max-h-60 border border-gray-700 rounded">
      <table class="min-w-full text-sm">
        <tbody class="text-sm">
        <tr v-for="(winner, index) in winners" :key="winner.id" class="text-sm">
          <td class="px-2 py-2">{{ winner.user }}</td>
          <td class="px-2 py-2">{{ winner.game }}</td>
          <td class="px-2 py-2">
            <button
                @click="rerollWinner(winner, index)"
                class="bg-blue-600 rounded p-1 btn-sm text-sm mr-2"
                :disabled="loading">
              <span v-if="loading">Loading...</span>
              <span v-else>Reroll</span>
            </button>
            <button
                @click="eliminateWinner(winner, index)"
                class="bg-red-600 rounded p-1 btn-sm"
                :disabled="loading">
              <span v-if="loading">Loading...</span>
              <span v-else>Elimină</span>
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- Battle Viewers Table Section -->
    <div class="overflow-auto max-h-60 border border-gray-700 rounded">
      <table class="min-w-full">
        <thead class="bg-gray-900">
        <tr>
          <th class="px-2 py-2 text-left text-gray-300">User</th>
          <th class="px-2 py-2 text-left text-gray-300">Joc</th>
          <th class="px-2 py-2 text-left text-gray-300">Ales</th>
          <th class="px-2 py-2 text-left text-gray-300">Eliminat</th>
        </tr>
        </thead>
        <tbody>
        <tr
            v-for="viewer in battleViewers"
            :key="viewer.id"
            :class="{
              'bg-green-600': viewer.picked && !viewer.eliminated,
              'bg-red-600': viewer.eliminated
            }"
        >
          <td class="px-2 py-2">{{ viewer.user }}</td>
          <td class="px-2 py-2">{{ viewer.game }}</td>
          <td class="px-2 py-2">{{ viewer.picked ? 'Yes' : 'No' }}</td>
          <td class="px-2 py-2">{{ viewer.eliminated ? 'Yes' : 'No' }}</td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- Control Buttons -->
    <div class="mt-4 flex space-x-4">
      <button @click="pickWinner" class="btn-primary" :disabled="loading">
        <span v-if="loading">Loading...</span>
        <span v-else>Alege Câștigător</span>
      </button>
      <button @click="fillList" class="btn-primary" :disabled="loading">
        <span v-if="loading">Loading...</span>
        <span v-else>Umple Lista</span>
      </button>
      <button @click="clearList" class="btn-danger" :disabled="loading">
        <span v-if="loading">Loading...</span>
        <span v-else>Șterge Lista</span>
      </button>
    </div>

    <p class="mb-2 text-gray-300 text-sm">
      Poți automatiza procesul de înscriere dacă folosești Streamlabs Cloud Bot.
    </p>

    <p class="mb-2 text-gray-300 text-sm">
      Creează comanda Streamlabs Coud Bot !particip:
    </p>
    <div class="mb-4 text-sm">
      <button
          class="w-full text-left p-2 bg-green-600 text-gray-900 rounded focus:outline-none hover:bg-green-500 transition"
          @click="copyText(apiUrlCommand)"
      >
        <code>{{ apiUrlCommand }}</code>
      </button>
    </div>

    <p class="mb-2 text-gray-300 text-sm">
      Creează comanda NightBot !particip:
    </p>
    <div class="mb-4 text-sm">
      <button
          class="w-full text-left p-2 bg-green-600 text-gray-900 rounded focus:outline-none hover:bg-green-500 transition"
          @click="copyText(apiUrlCommandNightBot)"
      >
        <code>{{ apiUrlCommandNightBot }}</code>
      </button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "BattleViewerList",
  emits: ["winnersPicked"],
  data() {
    return {
      battleViewers: [],
      winners: [],
      setting: false,
      loading: false, // Single global loading flag for all actions
    };
  },
  props: {
    fillCount: {
      type: Number,
      required: false,
    },
  },
  async mounted() {
    await this.fetchBattleViewers();
    await this.fetchSetting();
    window.Echo.channel(`App.Models.User.${this.$page.props.user.id}`)
        .listen('BattleViewerUpdated', () => {
          this.fetchBattleViewers();
        });
  },
  methods: {
    async fetchBattleViewers() {
      try {
        const response = await axios.get('api/bonus-battles/battle-viewers');
        this.battleViewers = response.data;
      } catch (error) {
        console.error('Failed to fetch battle viewers:', error.message);
      }
    },
    async fetchSetting() {
      if (this.loading) return;
      this.loading = true;
      try {
        const response = await axios.get(`/api/get-setting-public`, {
          params: { setting_name: 'battle_selections' },
        });
        if (response.data.setting_value !== undefined) {
          // Convert 1/0 to boolean
          this.setting = response.data.setting_value == 1;
        }
      } catch (error) {
        console.error('Failed to fetch setting:', error.message);
      } finally {
        this.loading = false;
      }
    },
    copyText(text) {
      if (navigator.clipboard) {
        navigator.clipboard
            .writeText(text)
            .then(() => {
              alert("Comanda a fost copiată în clipboard");
            })
            .catch((err) => {
              console.error("Failed to copy text: ", err);
            });
      } else {
        // Fallback for older browsers
        const input = document.createElement("input");
        input.value = text;
        document.body.appendChild(input);
        input.select();
        try {
          document.execCommand("copy");
        } catch (err) {
          console.error("Failed to copy text: ", err);
        }
        document.body.removeChild(input);
      }
    },
    // Emit the winners array (mapping to the user property)
    updateWinners() {
      this.$emit("winnersPicked", this.winners.map(w => w.user));
    },
    getRandomItem(array) {
      return array[Math.floor(Math.random() * array.length)];
    },
    async markAsPicked(viewerId) {
      try {
        await axios.patch(`api/bonus-battles/battle-viewers/${viewerId}`, {
          picked: true,
        });
      } catch (error) {
        console.error('Failed to mark as picked:', error.message);
      }
    },
    async markAsEliminated(viewerId) {
      try {
        await axios.patch(`api/bonus-battles/battle-viewers/${viewerId}`, {
          eliminated: true,
        });
      } catch (error) {
        console.error('Failed to mark as eliminated:', error.message);
      }
    },
    async pickWinner() {
      if (this.loading) return;
      this.loading = true;
      try {
        const candidates = this.battleViewers.filter(
            (viewer) => !viewer.picked && !viewer.eliminated
        );
        if (!candidates.length) {
          console.error('No available candidates');
          return;
        }
        const winner = this.getRandomItem(candidates);
        await this.markAsPicked(winner.id);
        winner.picked = true;
        this.winners.push(winner);
        this.updateWinners();
      } catch (error) {
        console.error("Error picking winner:", error);
      } finally {
        this.loading = false;
      }
    },
    async fillList() {
      if (this.loading) return;
      this.loading = true;
      try {
        if (this.fillCount < 0) return;
        const selectedWinners = [];
        for (let i = 0; i < this.fillCount; i++) {
          const candidates = this.battleViewers.filter(
              (viewer) => !viewer.picked && !viewer.eliminated
          );
          if (!candidates.length) break;
          const winner = this.getRandomItem(candidates);
          await this.markAsPicked(winner.id);
          winner.picked = true;
          selectedWinners.push(winner);
        }
        this.winners = selectedWinners;
        this.updateWinners();
      } catch (error) {
        console.error("Error filling list:", error);
      } finally {
        this.loading = false;
      }
    },
    clearList() {
      this.$dialog({
        message: "Ești sigur că vrei să faci o listă nouă?",
        buttons: ["da", "nu"],
        da: async () => {
          if (this.loading) return;
          this.loading = true;
          try {
            await axios.post('api/bonus-battles/battle-viewers/remove-all');
            this.battleViewers = [];
            this.winners = [];
            this.updateWinners();
          } catch (error) {
            console.error('Failed to clear the list:', error.message);
          } finally {
            this.loading = false;
          }
        },
        nu: () => console.log("Canceled reset")
      });
    },
    async rerollWinner(winner, index) {
      if (this.loading) return;
      this.loading = true;
      try {
        // Mark the winner being replaced as eliminated
        await this.markAsEliminated(winner.id);
        const existing = this.battleViewers.find((v) => v.id === winner.id);
        if (existing) {
          existing.eliminated = true;
        }
        const candidates = this.battleViewers.filter(
            (viewer) => !viewer.picked && !viewer.eliminated
        );
        if (!candidates.length) {
          console.error('No available candidates for reroll');
          return;
        }
        const newWinner = this.getRandomItem(candidates);
        await this.markAsPicked(newWinner.id);
        newWinner.picked = true;
        // Replace the old winner with the new one in the winners array
        this.winners.splice(index, 1, newWinner);
        this.updateWinners();
      } catch (error) {
        console.error("Error in rerollWinner:", error);
      } finally {
        this.loading = false;
      }
    },
    eliminateWinner(winner, index) {
      this.$dialog({
        message: "Ești sigur că vrei să elimini acest utilizator?",
        buttons: ["da", "nu"],
        da: async () => {
          if (this.loading) return;
          this.loading = true;
          try {
            await this.markAsEliminated(winner.id);
            this.winners.splice(index, 1);
            const viewer = this.battleViewers.find((v) => v.id === winner.id);
            if (viewer) {
              viewer.eliminated = true;
            }
            this.updateWinners();
          } catch (error) {
            console.error("Error eliminating winner:", error);
          } finally {
            this.loading = false;
          }
        },
        nu: () => console.log("Canceled elimination")
      });
    },
    async updateSetting() {
      if (this.loading) return;
      this.loading = true;
      try {
        await axios.post("/api/settings/save", {
          setting_name: "battle_selections",
          setting_value: this.setting,
        });
      } catch (error) {
        console.error("Error saving settings:", error);
      } finally {
        this.loading = false;
      }
    }
  },
  computed: {
    apiUrlCommand() {
      const host = window.location.origin;
      return `{readapi.${host}/api/add-bb-viewer/{user.name}/{1}/${this.$page.props.user.id}}`;
    },
    apiUrlCommandNightBot() {
      const host = window.location.origin;
      return `$(urlfetch ${host}/api/add-bb-viewer/$(user)/$(1) $(2) $(3)/${this.$page.props.user.id})`;
    },
  },
};
</script>

<style scoped>
table {
  font-size: 12px;
}
</style>
