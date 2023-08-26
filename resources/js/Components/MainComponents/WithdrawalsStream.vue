<template>
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label
                for="deposit"
                class="block mb-2 text-sm font-medium text-gray-900"
                >Retragere</label
            >
            <input
                v-model="newWithdrawal.amount"
                type="text"
                id="deposit"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Depus în lei"
            />
        </div>
        <div class="self-end">
            <button
                type="button"
                @click="createWithdraw"
                class="w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
                Withdraw
            </button>
        </div>
    </div>
    <div v-if="!loading" class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="p-6 text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr class="table-row">
                    <th class="p-2 table-cell text-left">ID</th>
                    <th class="p-2 table-cell text-left">Suma</th>
                    <th class="p-2 table-cell text-left">Data</th>
                </tr>
            </thead>
            <tbody>
                <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700" v-for="withdraw in withdrawals" :key="withdraw.id">
                    <td class="p-2 table-cell text-left">{{ withdraw.id }}</td>
                    <td class="p-2 table-cell text-left">{{ withdraw.amount }}</td>
                    <td class="p-2 table-cell text-left">{{ formatDateLabels(withdraw.created_at) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script >
import axios from "axios";
export default {
    props: {
        stream: Object,
    },
    data() {
        return {
            loading: true,
            deposits: [],
            newWithdrawal: {
                amount: 0,
                stream_id: null
            }
        };
    },
    async mounted() {
        await this.getStreamWithdrawals();
        this.loading = false;
    },
    methods: {
        async getStreamWithdrawals() {
            this.newWithdrawal.stream_id = this.stream.id;
            await axios
                .get("/api/withdrawals/" + this.stream.id)
                .then((response) => {
                    this.withdrawals = response.data.withdrawals;
                })
                .catch((error) => {
                    console.log(error);
                });
            this.loading = false;
        },
        async createWithdraw() {
            this.loading = true;
            let payload = this.newWithdrawal
            await axios
                .post("/api/withdrawals", {
                    payload,
                })
                .then(() => {
                    this.getStreamWithdrawals();
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        formatDateLabels(dateRaw) {
            const date = new Date(dateRaw);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is 0-indexed
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
    }
};
</script>
