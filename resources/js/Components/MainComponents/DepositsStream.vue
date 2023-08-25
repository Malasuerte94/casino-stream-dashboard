<template>
    <div class="grid grid-cols-3 gap-2">
        <div>
            <label
                for="deposit"
                class="block mb-2 text-sm font-medium text-gray-900"
                >Depozit</label
            >
            <input
                v-model="deposits"
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
                >Casino</label
            >
            <input
                v-model="deposits"
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
                Add
            </button>
        </div>
    </div>
        <table v-if="!loading">
            <tr>
                <th>ID</th>
                <th>Suma</th>
                <th>Casino</th>
                <th>Data</th>
            </tr>

            <tr v-for="deposit in deposits" :key="deposit.id">
                <td>{{ deposit.id }}</td>
                <td>{{ deposit.amount }}</td>
                <td>{{ deposit.casino }}</td>
                <td>{{ deposit.created_at }}</td>
            </tr>
        </table>
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
        };
    },
    async mounted() {
        await this.getStreamDeposit();
        this.loading = false;
    },
    methods: {
        async getStreamDeposit() {
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
            await axios
                .post("/api/deposits/new")
                .then((response) => {
                    this.deposits = response.data.deposits;
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    location.reload();
                });
            await this.getLatestStream();
        },
    }
};
</script>
