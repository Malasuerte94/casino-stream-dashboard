<template>
    <button
        @click="activateDialog"
        type="button"
        class="mb-10 w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
    >
        Stream Nou
    </button>
    <div class="grid grid-cols-3 gap-2">
        <div>
            <label
                for="deposit"
                class="block mb-2 text-sm font-medium text-gray-900"
                >Depozit</label
            >
            <input
                v-model="stream.deposit"
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
                v-model="stream.casino"
                type="text"
                id="casino"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Nume Casino"
            />
        </div>
        <div class="self-end">
            <button
                type="button"
                @click="updateStream"
                class="w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
                Update
            </button>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-2 mt-10">
        <div
            class="flex items-center pl-4 border bg-blue-400 border-gray-200 rounded dark:border-gray-700"
        >
            <input
                @change="updateSettings"
                id="bordered-radio-2"
                type="radio"
                value="buy"
                v-model="settings.bonus_list"
                name="bonus-list-display"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            />
            <label
                for="bordered-radio-2"
                class="w-full py-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                >Afișează Buy</label
            >
        </div>
        <div
            class="flex items-center pl-4 border bg-yellow-400 border-gray-200 rounded dark:border-gray-700"
        >
            <input
                @change="updateSettings"
                id="bordered-radio-1"
                type="radio"
                value="hunt"
                v-model="settings.bonus_list"
                name="bonus-list-display"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            />
            <label
                for="bordered-radio-1"
                class="w-full py-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                >Afișează Hunt</label
            >
        </div>
    </div>
</template>
<script >
import axios from "axios";
export default {
    data() {
        return {
            stream: [],
            settings: [],
        };
    },
    mounted() {
        this.getLatestStream();
        this.getSettings();
    },
    methods: {
        async getLatestStream() {
            await axios
                .get("/api/stream")
                .then((response) => {
                    this.stream = response.data.stream;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async getSettings() {
            await axios
                .get("/api/settings")
                .then((response) => {
                    this.settings = response.data.settings;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async updateSettings() {
            await axios
                .patch("/api/settings", {
                    settings: this.settings,
                })
                .then((response) => {
                    this.settings = response.data.settings;
                })
                .catch((error) => {
                    console.log(error);
                });
                await this.getSettings();
        },
        async updateStream() {
            await axios
                .patch("/api/stream/update", {
                    id: this.stream.id,
                    deposit: this.stream.deposit,
                    casino: this.stream.casino,
                })
                .then((response) => {
                    this.stream = response.data.stream;
                })
                .catch((error) => {
                    console.log(error);
                });
            await this.getLatestStream();
        },
        async createNewStream() {
            await axios
                .post("/api/stream/new")
                .then((response) => {
                    this.stream = response.data.stream;
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    location.reload();
                });
            await this.getLatestStream();
        },
        activateDialog() {
            this.$dialog({
                message:
                    "Ești sigur că vrei să începi un nou stream, acest lucru va reseta listele si resul optiunilor?",
                buttons: ["da", "nu"],
                da: () => {
                    this.createNewStream();
                },
                nu: () => {
                    console.log("refuse");
                },
            });
        },
    },
};
</script>
