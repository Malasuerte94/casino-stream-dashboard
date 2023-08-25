<template>
    <div class="bg-red-200 p-6 mb-12 rounded border">
        <button
        @click="activateDialog"
        type="button"
        class="w-full focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
    >
        Stream Nou
    </button>
    </div>
    <div v-if="!loading" class="bg-green-200 rounded border p-6">
        <DepositsStream :stream="stream"></DepositsStream>
    </div>
    <hr class="p-6 m-10">
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
import DepositsStream from './DepositsStream.vue';
export default {
    components: {DepositsStream},
    emits: ['displayOnly'],
    data() {
        return {
            loading: true,
            stream: [],
            settings: [],
        };
    },
    async mounted() {
        await this.getLatestStream();
        await this.getSettings();
        this.loading = false;
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
            if (this.settings.bonus_list) {
                this.$emit('displayOnly', this.settings.bonus_list);
            }
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

            if (this.settings.bonus_list) {
                this.$emit('displayOnly', this.settings.bonus_list);
            }
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
