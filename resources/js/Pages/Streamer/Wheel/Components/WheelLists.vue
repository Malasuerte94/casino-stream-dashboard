<template>
  <div class="flex flex-col items-center p-6 transition-all duration-300 w-2/6 bg-gray-800 rounded border border-gray-700 shadow">
    <!-- Loading Spinner -->
    <div v-if="loading" class="mb-4 text-center text-gray-300 transition-all duration-300">
      Loading...
    </div>
    <!-- Container for Textarea and Button -->
    <div class="w-full max-w-md space-y-4">
      <div class="space-y-2">
        <label for="games-input" class="block text-lg font-medium text-gray-300 transition-all duration-300">
          Lista Roată
        </label>
        <textarea
            rows="20"
            id="games-input"
            v-model="wheelList"
            class="w-full p-4 bg-gray-800 text-gray-100 border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300"
            placeholder="Enter game names, each on a new line..."
        ></textarea>
      </div>
      <!-- Centered Button with fixed padding -->
      <div class="flex justify-center">
        <button
            @click="updateWheelList"
            :disabled="loading"
            class="btn-primary px-8 py-2 transition-all duration-300"
        >
          Update
        </button>
      </div>
    </div>
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
        const response = await axios.get("api/wheel-list/" + this.$page.props.user.id);
        this.wheelList = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async updateWheelList() {
      this.loading = true;
      try {
        await axios.patch("api/wheel-list", { wheelList: this.wheelList });
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
/* Container background and transitions */
.bg-gray-900 {
  /* Dark background for the page */
}

/* Button Styles */
.btn-primary {
  @apply bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300;
  @apply dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800;
}

/* Ensure the textarea uses a dark style */
textarea {
  @apply bg-gray-800 text-gray-100 border border-gray-600 rounded-lg shadow-sm p-4 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
