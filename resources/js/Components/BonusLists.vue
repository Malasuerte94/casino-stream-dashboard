<template>
    <div>
        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
            <div class="p-6">
                <div class="grid grid-cols-2 gap-2">
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
            </div>

            <div v-if="settings.bonus_list == 'buy'" class="p-6">
                <h5 class="text-lg font-bold">Bonus Buy - List</h5>
                <BonusBuy />
            </div>

            <div v-if="settings.bonus_list == 'hunt'" class="p-6">
                <h5 class="text-lg font-bold">Bonus Hunt - List</h5>
                <BonusHunt />
            </div>
        </div>
    </div>
</template>
<script >
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import BonusBuy from "@/Components/MainComponents/BonusBuy.vue";
import BonusHunt from "@/Components/MainComponents/BonusHunt.vue";
import StreamBoard from "@/Components/MainComponents/StreamBoard.vue";
import { initFlowbite } from "flowbite";
export default {
    components: { ApplicationLogo, BonusBuy, BonusHunt, StreamBoard },
    data() {
        return {
            settings: [],
        };
    },
    async mounted() {
        await this.getSettings();
        initFlowbite();
    },
    methods: {
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
        },
    },
};
</script>
