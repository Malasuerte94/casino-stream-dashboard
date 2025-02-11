<template>
  <div class="p-2 rounded-md shadow-md md:w-1/3 bg-gray-800 space-y-4 transition-all duration-300">
    <h3 class="text-lg font-semibold text-gray-300">Listă Useri Înscriși din YT</h3>
    <!-- Winners Table Section (if any winners have been picked) -->
    <div v-if="winners.length" class="overflow-auto max-h-60 border border-gray-700 rounded">
      <table class="min-w-full text-sm">
        <tbody class="text-sm">
        <tr
            v-for="(winner, index) in winners"
            :key="winner.id"
            class="text-sm"
        >
          <td class="px-2 py-2">{{ winner.user }}</td>
          <td class="px-2 py-2">{{ winner.game }}</td>
          <td class="px-2 py-2">
            <button @click="rerollWinner(winner, index)" class="bg-blue-600 rounded p-1 btn-sm text-sm mr-2">
              Reroll
            </button>
            <button @click="eliminateWinner(winner, index)" class="bg-red-600 rounded p-1 btn-sm">
              Elimină
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
      <button @click="pickWinner" class="btn-primary">Alege Câștigător</button>
      <button @click="fillList" class="btn-primary">Umple Lista</button>
      <button @click="clearList" class="btn-danger">Șterge Lista</button>
    </div>
    <p>
      Poți automatiza procesul de înscriere dacă folosești Streamlabs Cloud Bot.
      Creează comanda
      <span class="text-sm bg-green-600 p-1 rounded">
        {{apiUrlCommand}}
      </span>
    </p>
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
      winners: []
    };
  },
  props: {
    fillCount: {
      type: Number,
      required: false
    }
  },
  mounted() {
    this.fetchBattleViewers();
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
    // Helper to emit the full winners array (mapped to the user property)
    updateWinners() {
      this.$emit("winnersPicked", this.winners.map(w => w.user));
    },
    getRandomItem(array) {
      return array[Math.floor(Math.random() * array.length)];
    },
    async markAsPicked(viewerId) {
      try {
        await axios.patch(`api/bonus-battles/battle-viewers/${viewerId}`, {
          picked: true
        });
      } catch (error) {
        console.error('Failed to mark as picked:', error.message);
      }
    },
    async markAsEliminated(viewerId) {
      try {
        await axios.patch(`api/bonus-battles/battle-viewers/${viewerId}`, {
          eliminated: true
        });
      } catch (error) {
        console.error('Failed to mark as eliminated:', error.message);
      }
    },
    async pickWinner() {
      const candidates = this.battleViewers.filter(
          viewer => !viewer.picked && !viewer.eliminated
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
    },
    async fillList() {
      if(this.fillCount < 0) return;
      const selectedWinners = [];
      for (let i = 0; i < this.fillCount; i++) {
        const candidates = this.battleViewers.filter(
            viewer => !viewer.picked && !viewer.eliminated
        );
        if (!candidates.length) break;
        const winner = this.getRandomItem(candidates);
        await this.markAsPicked(winner.id);
        winner.picked = true;
        selectedWinners.push(winner);
      }
      this.winners = selectedWinners;
      this.updateWinners();
    },
    clearList() {
      this.$dialog({
        message: "Ești sigur că vrei să faci o listă nouă?",
        buttons: ["da", "nu"],
        da: async () => {
          try {
            await axios.post('api/bonus-battles/battle-viewers/remove-all');
            this.battleViewers = [];
            this.winners = [];
            this.updateWinners();
          } catch (error) {
            console.error('Failed to clear the list:', error.message);
          }
        },
        nu: () => console.log("Canceled reset")
      });
    },
    async rerollWinner(winner, index) {
      // Mark the winner being replaced as eliminated
      await this.markAsEliminated(winner.id);
      const existing = this.battleViewers.find(v => v.id === winner.id);
      if (existing) {
        existing.eliminated = true;
      }
      // Now, pick a new candidate from those not picked and not eliminated
      const candidates = this.battleViewers.filter(
          viewer => !viewer.picked && !viewer.eliminated
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
    },
    eliminateWinner(winner, index) {
      this.$dialog({
        message: "Ești sigur că vrei să elimini acest utilizator?",
        buttons: ["da", "nu"],
        da: async () => {
          await this.markAsEliminated(winner.id);
          this.winners.splice(index, 1);
          const viewer = this.battleViewers.find(v => v.id === winner.id);
          if (viewer) {
            viewer.eliminated = true;
          }
          this.updateWinners();
        },
        nu: () => console.log("Canceled elimination")
      });
    }
  },
  computed: {
    apiUrlCommand() {
      const host = window.location.origin;
      return `{readapi.${host}/api/add-bb-viewer/{user.name}/{target.name}/${this.$page.props.user.id}}`;
    },
  },
};
</script>


<style scoped>
table {
  font-size: 12px;
}
</style>
