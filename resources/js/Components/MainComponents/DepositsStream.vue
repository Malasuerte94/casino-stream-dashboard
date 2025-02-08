<template>
  <div>
    <h2 class="text-lg font-bold mb-4 text-gray-200">Depozit</h2>

    <!-- Deposit Form Card -->
    <transition name="fade">
      <div class="bg-gray-800 p-6 mb-6 rounded-lg border border-gray-700 shadow-lg">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label for="deposit" class="block mb-2 text-sm font-medium text-gray-300">
              Depozit
            </label>
            <input
                v-model="newDeposit.amount"
                type="text"
                id="deposit"
                class="input-primary"
                placeholder="Depus în lei"
            />
          </div>
          <div>
            <label for="casino" class="block mb-2 text-sm font-medium text-gray-300">
              Pe Casino
            </label>
            <input
                v-model="newDeposit.casino"
                type="text"
                id="casino"
                class="input-primary"
                placeholder="Nume Casino"
            />
          </div>
          <div class="self-end">
            <button
                type="button"
                @click="createDeposit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition-all duration-200 focus:outline-none"
            >
              Depozitare
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Deposits Table -->
    <transition name="fade">
      <div v-if="!loading" class="relative overflow-x-auto shadow-lg sm:rounded-lg mt-6">
        <table class="w-full text-sm text-left text-gray-300">
          <thead class="text-xs uppercase bg-gray-700 text-gray-400">
          <tr>
            <th class="p-2">ID</th>
            <th class="p-2">Suma</th>
            <th class="p-2">Casino</th>
            <th class="p-2">Data</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="deposit in deposits" :key="deposit.id" class="bg-gray-800 border-b border-gray-700">
            <td class="p-2">{{ deposit.id }}</td>
            <td class="p-2">{{ deposit.amount }}</td>
            <td class="p-2">{{ deposit.casino }}</td>
            <td class="p-2">{{ formatDateLabels(deposit.created_at) }}</td>
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
      deposits: [],
      newDeposit: {
        amount: 0,
        casino: "",
        stream_id: null,
      },
    };
  },
  async mounted() {
    await this.getStreamDeposit();
    this.loading = false;
  },
  methods: {
    async getStreamDeposit() {
      this.newDeposit.stream_id = this.stream.id;
      try {
        const response = await axios.get("/api/deposits/" + this.stream.id);
        this.deposits = response.data.deposits;
      } catch (error) {
        console.log(error);
      }
    },
    async createDeposit() {
      this.loading = true;
      try {
        await axios.post("/api/deposits", {
          payload: this.newDeposit,
        });
        await this.getStreamDeposit();
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
