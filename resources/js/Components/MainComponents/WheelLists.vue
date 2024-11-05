<template>
  <div class="flex flex-col items-center p-6 space-y-4 bg-gray-100 min-h-screen">
    <!-- Loading Spinner (if loading) -->
    <div v-if="loading" class="text-center text-gray-500">Loading...</div>

    <!-- Games Input Section -->
    <div class="w-full max-w-md space-y-2">
      <label for="games-input" class="block text-lg font-medium text-gray-700">Game List</label>
      <textarea
          rows="20"
          id="games-input"
          v-model="wheelList"
          class="w-full h-100 p-4 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Enter game names, each on a new line..."
      ></textarea>
    </div>
    <!-- Update Button -->
    <button
        @click="updateWheelList"
        :disabled="loading"
        class="btn-big btn-primary w-full"
    >
      Update
    </button>

  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      loading: true,
      wheelList: '',
    };
  },

  async mounted() {
    await this.getWheelList();
    this.loading = false;
  },

  methods: {
    async getWheelList() {
      try {
        const response = await axios.get("api/wheel-list/"+this.$page.props.user.id);
        this.wheelList = response.data;
      } catch (error) {
        console.error(error);
      }
    },

    async updateWheelList() {
      this.loading = true;
      try {
        await axios.patch("api/wheel-list", {wheelList: this.wheelList});
        await this.getWheelList();
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
/* Additional styling if needed */
</style>
