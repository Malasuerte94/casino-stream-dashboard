<template>
  <div>
    <h2 class="text-lg font-bold mb-4 text-gray-200">Retragere</h2>
    <!-- Withdrawal Form Card -->
    <transition name="fade">
      <div class="bg-gray-700 p-6 mb-6 rounded-lg border border-gray-700 shadow-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="withdraw" class="block mb-2 text-sm font-medium text-gray-300">
              Sumă retragere
            </label>
            <input
                v-model="newWithdrawal.amount"
                type="text"
                id="withdraw"
                class="input-primary"
                placeholder="Retragere în lei"
            />
          </div>
          <div class="self-end">
            <button
                type="button"
                @click="createWithdraw"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition-all duration-200 focus:outline-none"
            >
              Confirmă
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Withdrawals Table -->
    <transition name="fade">
      <div v-if="!loading" class="relative overflow-x-auto shadow-lg sm:rounded-lg mt-6">
        <table class="w-full text-sm text-left text-gray-300">
          <thead class="text-xs uppercase bg-gray-700 text-gray-400">
          <tr>
            <th class="p-2">ID</th>
            <th class="p-2">Suma</th>
            <th class="p-2">Data</th>
          </tr>
          </thead>
          <tbody>
          <tr
              v-for="withdraw in withdrawals"
              :key="withdraw.id"
              class="bg-gray-800 border-b border-gray-700"
          >
            <td class="p-2">{{ withdraw.id }}</td>
            <td class="p-2">{{ withdraw.amount }}</td>
            <td class="p-2">{{ formatDateLabels(withdraw.created_at) }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from "axios";
export default {
  props: {
    stream: Object,
  },
  data() {
    return {
      loading: true,
      withdrawals: [],
      newWithdrawal: {
        amount: 0,
        stream_id: null,
      },
    };
  },
  async mounted() {
    await this.getStreamWithdrawals();
    this.loading = false;
  },
  methods: {
    async getStreamWithdrawals() {
      this.newWithdrawal.stream_id = this.stream.id;
      try {
        const response = await axios.get("/api/withdrawals/" + this.stream.id);
        this.withdrawals = response.data.withdrawals;
      } catch (error) {
        console.log(error);
      }
      this.loading = false;
    },
    async createWithdraw() {
      this.loading = true;
      try {
        await axios.post("/api/withdrawals", {payload: this.newWithdrawal});
        await this.getStreamWithdrawals();
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    formatDateLabels(dateRaw) {
      const date = new Date(dateRaw);
      const day = date.getDate().toString().padStart(2, "0");
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const year = date.getFullYear();
      return `${day}-${month}-${year}`;
    },
  },
};
</script>

<style scoped>
/* Fade Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
