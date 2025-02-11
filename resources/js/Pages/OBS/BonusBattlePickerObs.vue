<template>
  <div v-if="!loading" class="p-4 text-center">
    <!-- Winners Section -->
    <div class="mb-6 flex flex-col items-center" v-if="winners.length">
      <h2 class="text-3xl font-extrabold text-yellow-400 uppercase mb-2">Câștigători</h2>
      <div class="flex justify-center flex-wrap gap-4">
        <div
            v-for="winner in winners"
            :key="winner.id"
            class="px-4 py-2 bg-yellow-600 text-white rounded-lg shadow-xl transform transition-all hover:scale-105"
        >
          <p class="text-xl font-bold">{{ winner.user }}</p>
          <p class="text-sm">{{ winner.game }}</p>
        </div>
      </div>
    </div>

    <!-- Participants Section as Labels -->
    <div>
      <h2 class="text-xl font-bold text-white bg-gray-800 rounded mb-3 p-2">Participanți</h2>
      <div class="flex flex-wrap gap-1 justify-center">
        <div
            v-for="viewer in allViewers"
            :key="viewer.id"
            class="px-2 py-1 rounded shadow-md bg-gray-800 text-white text-sm"
            :class="{
            'bg-green-400 font-bold': viewer.picked && !viewer.eliminated,
            'bg-red-500 font-bold': viewer.eliminated
          }"
        >
          {{ viewer.user }}
        </div>
      </div>
    </div>
  </div>
  <div v-else class="p-4">
    <p class="text-white text-lg">Loading...</p>
  </div>
</template>

<script>
export default {
  props: ["id"],
  data() {
    return {
      loading: true,
      allViewers: [],
    };
  },
  computed: {
    // Compute winners from the allViewers list (those that are picked and not eliminated)
    winners() {
      return this.allViewers.filter(
          (viewer) => viewer.picked && !viewer.eliminated
      );
    },
  },
  async mounted() {
    await this.getList();
    this.loading = false;
    // Listen for real-time updates on the public channel for this user.
    window.Echo.channel(`App.Models.User.${this.id}`)
        .listen("BattleViewerUpdated", async () => {
          await this.updateList();
        });
  },
  methods: {
    async getList() {
      try {
        const response = await axios.get(`/api/battle-viewers-public/${this.id}`);
        if (response.data) {
          this.allViewers = response.data;
        }
        console.log(this.allViewers);
      } catch (error) {
        console.log(error);
      }
    },
    async updateList() {
      await this.getList();
    },
  },
};
</script>

<style lang="scss">
.footer-donate {
  display: none !important;
}

body,
#app {
  font-size: 1.3rem;
  width: 400px;
  background-color: transparent !important;
}

/* Optional additional styling for a streaming overlay look */
</style>
