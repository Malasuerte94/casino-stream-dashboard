<template>
  <div class="p-6">
    <!-- Add New Stream Account Form -->
    <div class="mb-8 bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-4">Add New Stream Account</h2>
      <form @submit.prevent="createNewStreamAccount" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
            <select
                v-model="newAccount.type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
              <option value="youtube">YouTube</option>
              <option value="kick">Kick</option>
              <option value="facebook">Facebook</option>
              <option value="twitch">Twitch</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stream URL</label>
            <input
                type="url"
                v-model="newAccount.url"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
                placeholder="https://"
            >
          </div>
        </div>
        <div class="flex justify-end">
          <button
              type="submit"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              :disabled="loading"
          >
            {{ loading ? 'Adding...' : 'Add Stream Account' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Existing Stream Accounts -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-4">Your Stream Accounts</h2>

      <div v-if="loading" class="text-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
      </div>

      <div v-else-if="streamAccounts.length === 0" class="text-center py-4 text-gray-500">
        No stream accounts added yet.
      </div>

      <div v-else class="space-y-4">
        <div v-for="account in streamAccounts" :key="account.id"
             class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
          <div>
            <span class="font-medium capitalize">{{ account.type }}</span>
            <a :href="account.url" target="_blank" class="text-indigo-600 ml-2 hover:underline">
              {{ account.url }}
            </a>
          </div>
          <button
              @click="deleteStreamAccount(account.id)"
              class="text-red-600 hover:text-red-800 focus:outline-none"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios"

export default {
  data() {
    return {
      loading: true,
      streamAccounts: [],
      newAccount: {
        type: 'youtube',
        url: '',
      },
    };
  },

  async mounted() {
    await this.getStreamAccounts();
    this.loading = false;
  },

  methods: {
    async getStreamAccounts() {
      try {
        const response = await axios.get("/api/stream-accounts");
        this.streamAccounts = response.data.streamAccounts;
      } catch (error) {
        console.error(error);
      }
    },

    async createNewStreamAccount() {
      this.loading = true;
      try {
        await axios.post("/api/stream-accounts/new", this.newAccount);
        await this.getStreamAccounts();
        this.newAccount.url = ''; // Reset form
        // Show success message if you have a notification system
      } catch (error) {
        console.error(error);
        // Show error message if you have a notification system
      } finally {
        this.loading = false;
      }
    },

    async deleteStreamAccount(id) {
      if (!confirm('Are you sure you want to delete this stream account?')) return;

      this.loading = true;
      try {
        await axios.delete(`/api/stream-accounts/${id}`);
        await this.getStreamAccounts();
        // Show success message if you have a notification system
      } catch (error) {
        console.error(error);
        // Show error message if you have a notification system
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>