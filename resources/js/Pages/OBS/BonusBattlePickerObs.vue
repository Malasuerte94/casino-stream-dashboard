<template>
  <div v-if="!loading && allSettings.battle_selections == 1" class="p-4 text-center">
    <!-- Winners Section -->
    <div class="mb-6 flex flex-col items-center" v-if="winners.length">
      <h2 class="text-2xl font-extrabold text-yellow-400 uppercase mb-2">Câștigători</h2>
      <div class="flex justify-center flex-wrap gap-4">
        <div
            v-for="winner in winners"
            :key="winner.id"
            class="px-2 py-1 bg-yellow-600 text-white rounded-lg shadow-xl transform transition-all hover:scale-105"
        >
          <p class="text-sm font-bold">{{ winner.user }}</p>
          <p class="text-xs">{{ winner.game }}</p>
        </div>
      </div>
    </div>

    <!-- Participants Section as Labels -->
    <div class="flex flex-col items-center">
      <BonusBattlePickerUsersGlobe v-if="allViewers.length" :allViewers="allViewers" />
    </div>
  </div>
  <div v-else class="p-4"></div>
</template>

<script>
import BonusBattlePickerUsersGlobe from "@/Pages/OBS/Components/BonusBattlePickerUsersGlobe.vue";

export default {
  components: {BonusBattlePickerUsersGlobe},
  props: ["id"],
  data() {
    return {
      loading: true,
      allViewers: [],
      allSettings: {},
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
    await this.updateList();
    this.loading = false;
    // Listen for real-time updates on the public channel for this user.
    window.Echo.channel(`App.Models.User.${this.id}`)
        .listen("BattleViewerUpdated", async () => {
          await this.updateList();
        }).listen("SettingsUpdated", async () => {
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
      } catch (error) {
        console.log(error);
      }
    },
    async getSettings() {
      await axios
          .get("/api/settings/" + this.id)
          .then((response) => {
            this.allSettings = response.data.settings;
          })
          .catch((error) => {
            console.log(error);
          });
    },
    async updateList() {
      await this.getList();
      await this.getSettings();
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
  width: 800px;
  background-color: transparent !important;
}

/* Optional additional styling for a streaming overlay look */
</style>
