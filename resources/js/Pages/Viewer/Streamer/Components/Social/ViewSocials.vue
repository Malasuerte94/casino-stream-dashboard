<template>
  <div class="py-2 text-gray-200 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-lg backdrop-blur-xl p-4 bg-white/10 shadow-lg shadow-black/40 border border-white/20">
        <!-- Content Rows -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" v-if="donations.length > 0">
          <!-- Full Referral List -->
          <div class="bg-gray-800 p-4 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Donații</h2>
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-800 text-gray-300">
              <tr>
                <th class="px-4 py-2">Nume</th>
                <th class="px-4 py-2">Sumă</th>
                <th class="px-4 py-2">Mesaj</th>
              </tr>
              </thead>
              <tbody>
              <tr
                  v-for="donation in donations"
                  :key="donation.donation_id"
                  class="hover:bg-gray-600"
              >
                <td class="px-4 py-2">{{ donation.name }}</td>
                <td class="px-4 py-2">{{ parseFloat(donation.amount).toFixed(2) }} {{donation.currency}}</td>
                <td class="px-4 py-2">{{ donation.message }}</td>
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
    streamerId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      donations: [],
    };
  },
  async mounted() {
    await this.getDonations();
  },
  methods: {
    async getDonations() {
      try {
        const response = await axios.get(`/api/viewer/get-donations/${this.streamerId}`);
        this.donations = response.data.donations;
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>
