<template>
  <div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div>
          <div class="p-6">
            <h1 class="text-3xl font-bold">Referrals pentru {{ user }}</h1>
          </div>
        </div>
        <div class="mb-4 p-4">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Parent User
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Referred User
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created At
              </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="referral in list" :key="referral.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ referral.parent_user }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ referral.referred_user }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ new Date(referral.created_at).toLocaleDateString() }}
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["id"],
  data() {
    return {
      list: [],
      user: '',
    };
  },
  async mounted() {
    await this.getReferrals();
  },
  methods: {
    async getReferrals() {
      await axios
          .get("/api/ref-list/" + this.id)
          .then((response) => {
            this.list = response.data.list;
            this.user = response.data.user;
          })
          .catch((error) => {
            console.log(error);
          });
    },
  },
};
</script>
