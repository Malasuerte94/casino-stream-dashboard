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
                <StreamBoard />
            </div>

            <div id="accordion-collapse" data-accordion="collapse">
                <h2 id="accordion-collapse-heading-1">
                    <button
                        type="button"
                        class="flex items-center justify-between w-full p-5 font-medium text-left text-black border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 bg-blue-400 hover:bg-gray-500 dark:hover:bg-gray-800"
                        data-accordion-target="#accordion-collapse-body-1"
                        aria-expanded="true"
                        aria-controls="accordion-collapse-body-1"
                    >
                        <h5 class="text-lg font-bold">Bonus Buy - List</h5>
                        <svg
                            data-accordion-icon
                            class="w-3 h-3 rotate-180 shrink-0"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5 5 1 1 5"
                            />
                        </svg>
                    </button>
                </h2>
                <div
                    id="accordion-collapse-body-1"
                    class="hidden"
                    aria-labelledby="accordion-collapse-heading-1"
                >
                    <div
                        class="p-5 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900"
                    >
                        <BonusBuy />
                    </div>
                </div>
                <h2 id="accordion-collapse-heading-2">
                    <button
                        type="button"
                        class="flex items-center justify-between w-full p-5 font-medium text-left text-black border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 bg-yellow-400 hover:bg-yellow-500 dark:hover:bg-yellow-800"
                        data-accordion-target="#accordion-collapse-body-2"
                        aria-expanded="false"
                        aria-controls="accordion-collapse-body-2"
                    >
                        <h5 class="text-lg font-bold">Bonus Hunt - List</h5>
                        <svg
                            data-accordion-icon
                            class="w-3 h-3 rotate-180 shrink-0"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5 5 1 1 5"
                            />
                        </svg>
                    </button>
                </h2>
                <div
                    id="accordion-collapse-body-2"
                    class="hidden"
                    aria-labelledby="accordion-collapse-heading-2"
                >
                    <div
                        class="p-5 border border-b-0 border-gray-200 dark:border-gray-700"
                    >
                        <BonusHunt />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script >
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import BonusBuy from "@/Components/MainComponents/BonusBuy.vue";
import BonusHunt from "@/Components/MainComponents/BonusHunt.vue";
import StreamBoard from "@/Components/MainComponents/StreamBoard.vue";
import { initFlowbite } from 'flowbite'
export default {
    components: {ApplicationLogo, BonusBuy, BonusHunt, StreamBoard},
    mounted() {
        initFlowbite();
    },
}
</script>
