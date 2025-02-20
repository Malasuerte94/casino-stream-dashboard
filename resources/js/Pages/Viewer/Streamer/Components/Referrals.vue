<template>
  <div class="py-2 text-gray-200 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-lg backdrop-blur-xl p-4 bg-white/10 shadow-lg shadow-black/40 border border-white/20">
        <!-- Page Title -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold">Referrals pentru {{ user }}</h1>
          <div class="flex space-x-4 items-center">
            <label for="fromDate" class="text-sm font-medium">From:</label>
            <input
                id="fromDate"
                type="date"
                v-model="filter.from"
                class="bg-gray-700 text-gray-200 px-3 py-1 rounded-md border border-gray-600"
            />
            <label for="toDate" class="text-sm font-medium">To:</label>
            <input
                id="toDate"
                type="date"
                v-model="filter.to"
                class="bg-gray-700 text-gray-200 px-3 py-1 rounded-md border border-gray-600"
            />
            <button
                @click="filterReferrals"
                class="btn-primary">
              Filter
            </button>
          </div>
        </div>

        <!-- Content Rows -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Referrers Leaderboard -->
          <div class="bg-gray-900 p-4 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Leaderboard</h2>
            <ul>
              <li
                  v-for="(referrer, index) in topReferrers"
                  :key="referrer.parent_user"
                  class="flex justify-between items-center p-2 rounded-md hover:bg-gray-600"
              >
                <span>
                  <span v-if="index === 0">🥇</span>
                  <span v-else-if="index === 1">🥈</span>
                  <span v-else-if="index === 2">🥉</span>
                  {{ referrer.parent_user }}
                </span>
                <span>{{ referrer.count }} Referrals</span>
              </li>
            </ul>
          </div>

          <!-- Full Referral List -->
          <div class="bg-gray-800 p-4 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">All Referrals</h2>
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-800 text-gray-300">
              <tr>
                <th class="px-4 py-2">Parent User</th>
                <th class="px-4 py-2">Referred User</th>
                <th class="px-4 py-2">Created At</th>
              </tr>
              </thead>
              <tbody>
              <tr
                  v-for="referral in filteredList"
                  :key="referral.id"
                  class="hover:bg-gray-600"
              >
                <td class="px-4 py-2">{{ referral.parent_user }}</td>
                <td class="px-4 py-2">{{ referral.referred_user }}</td>
                <td class="px-4 py-2">
                  {{ referral.created_at ? new Date(referral.created_at).toLocaleDateString() : 'N/A' }}
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    steamerId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      list: [],
      topReferrers: [],
      filteredList: [],
      user: '',
      filter: {
        from: '',
        to: '',
      },
    };
  },
  async mounted() {
    await this.getReferrals();
  },
  methods: {
    async getReferrals() {
      try {
        const response = await axios.get(`/api/ref-list/${this.steamerId}`);
        this.list = response.data.list;
        this.user = response.data.user;
        this.filteredList = this.list;
        this.calculateTopReferrers();
      } catch (error) {
        console.log(error);
      }
    },
    calculateTopReferrers() {
      const referrerCounts = this.list.reduce((acc, referral) => {
        acc[referral.parent_user] = (acc[referral.parent_user] || 0) + 1;
        return acc;
      }, {});
      this.topReferrers = Object.entries(referrerCounts)
          .map(([parent_user, count]) => ({parent_user, count}))
          .sort((a, b) => b.count - a.count); // Sort descending by count
    },
    filterReferrals() {
      const from = new Date(this.filter.from);
      const to = new Date(this.filter.to);
      this.filteredList = this.list.filter((referral) => {
        const createdAt = referral.created_at ? new Date(referral.created_at) : null;
        return (!from || !createdAt || createdAt >= from) &&
            (!to || !createdAt || createdAt <= to);
      });
    },
  },
};
</script>

<style scoped>
table th,
table td {
  padding: 0.5rem;
}

table tbody tr:hover {
  background-color: #374151;
}
</style>
