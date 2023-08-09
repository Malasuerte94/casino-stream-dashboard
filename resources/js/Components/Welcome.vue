<template>
    <div>
        <div class="p-6 bg-white border-b border-gray-200">
            <div>
                <ApplicationLogo class="block h-12 w-auto" />
            </div>

            <div class="mt-2 text-2xl">Welcome to your Păcănele Dashboard</div>

            <div class="mt-2 text-gray-500">
                Acest program a fost dezvoltat de
                <a target="blank" href="https://www.youtube.com/@MalaTheMan"
                    >@MalaTheMan</a
                >
            </div>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
            <div class="p-6 mb-10 mt-10">
                <StreamBoard @displayOnly="showOnly($event)" />
            </div>

            <div v-if="showForm == 'buy'" class="p-6">
                <h5 class="text-lg font-bold">Bonus Buy - List</h5>
                <BonusBuy />
            </div>

            <div v-if="showForm == 'hunt'" class="p-6">
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
            showForm: 'hunt',
        };
    },
    mounted() {
        initFlowbite();
    },
    methods: {
        showOnly($form) {
            this.showForm = $form;
        },
    },
};
</script>
