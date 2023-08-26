<template>
    <h2 class="text-lg font-bold mb-4">Depozit</h2>
    <div class="grid grid-cols-3 gap-2">
        <div>
            <label
                for="deposit"
                class="block mb-2 text-sm font-medium text-gray-900"
                >Depozit</label
            >
            <input
                v-model="newDeposit.amount"
                type="text"
                id="deposit"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Depus în lei"
            />
        </div>
        <div>
            <label
                for="casino"
                class="block mb-2 text-sm font-medium text-gray-900"
                >Pe Casino</label
            >
            <input
                v-model="newDeposit.casino"
                type="text"
                id="casino"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Nume Casino"
            />
        </div>
        <div class="self-end">
            <button
                type="button"
                @click="createDeposit"
                class="w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
                Depozitare
            </button>
        </div>
    </div>
    <div v-if="!loading" class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="p-6 text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr class="table-row">
                    <th class="p-2 table-cell text-left">ID</th>
                    <th class="p-2 table-cell text-left">Suma</th>
                    <th class="p-2 table-cell text-left">Casino</th>
                    <th class="p-2 table-cell text-left">Data</th>
                </tr>
            </thead>
            <tbody>
                <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700" v-for="deposit in deposits" :key="deposit.id">
                    <td class="p-2 table-cell text-left">{{ deposit.id }}</td>
                    <td class="p-2 table-cell text-left">{{ deposit.amount }}</td>
                    <td class="p-2 table-cell text-left">{{ deposit.casino }}</td>
                    <td class="p-2 table-cell text-left">{{ formatDateLabels(deposit.created_at) }}</td>
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
            newDeposit: {
                amount: 0,
                casino: '',
                stream_id: null
            }
        };
    },
    async mounted() {
        await this.getStreamDeposit();
        this.loading = false;
    },
    methods: {
        async getStreamDeposit() {
            this.newDeposit.stream_id = this.stream.id;
            await axios
                .get("/api/deposits/" + this.stream.id)
                .then((response) => {
                    this.deposits = response.data.deposits;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async createDeposit() {
            this.loading = true;
            let payload = this.newDeposit
            await axios
                .post("/api/deposits", {
                    payload,
                })
                .then(() => {
                    this.getStreamDeposit();
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
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
