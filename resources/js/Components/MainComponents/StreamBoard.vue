<template>
    <div class="p-6">
        <div class="bg-red-200 p-6 mb-6 rounded border">
            <button
            @click="activateDialog"
            type="button"
            class="w-full focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
        >
            Stream Nou
            </button>
            <div class="text-sm mt-4 text-red-500">
                Înainte de fiecare stream te rog să apeși butonul de <b>Stream Nou</b>, introdu suma de pe care o ai depozitată pe casino și poți începe, dacă vrei să faci o retragere introdu și retragerea și confirmă pentru a avea un raport cât mai corect.
            </div>
        </div>
        <div class="grid justify-between grid-cols-2 gap-2">
            <div v-if="!loading" class="box-secondary w-50 block">
                <DepositsStream :stream="stream"></DepositsStream>
            </div>
            <div v-if="!loading" class="box-secondary rounded border p-6 w-50 block">
                <WithdrawalsStream :stream="stream"></WithdrawalsStream>
            </div>
        </div>
    </div>
</template>
<script >
import axios from "axios"
import DepositsStream from './DepositsStream.vue'
import WithdrawalsStream from './WithdrawalsStream.vue'
export default {
    components: {DepositsStream, WithdrawalsStream},
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
