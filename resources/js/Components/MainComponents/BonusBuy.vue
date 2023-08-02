<template>
    <div class="grid grid-cols-[minmax(200px,_1fr)_120px_120px_120px] gap-2 mt-2 mb-2">
        <input
            @input="startTimerUpdateBonusBuy"
            type="text"
            id="bonus_buy_name"
            v-model="bonusBuy.name"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Nume Lista"
        />
        <input
            @input="startTimerUpdateBonusBuy"
            type="number"
            id="bonus_buy_start"
            v-model="bonusBuy.start"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Cost (LEI)"
        />
        <input
            disabled
            type="number"
            id="bonus_buy_result"
            v-model="bonusBuy.result"
            class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Rezultat (LEI)"
        />
        <button
            @click="resetBonusBuy"
            type="button"
            tabindex="-1"
            class="justify-center self-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
        >
            CLEAR
        </button>
    </div>
    <template v-if="bonusBuyGames">
        <div
            class="grid grid-cols-[30px_minmax(200px,_1fr)_120px_120px_120px_120px_80px] gap-2 mt-4 text-center"
        >
            <div
                class="w-8 h-8 justify-center self-center text-white bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:focus:ring-blue-800"
            >
                nr
            </div>
            <div>Joc</div>
            <div>Miză</div>
            <div>Preț (LEI)</div>
            <div>Rezultat (LEI)</div>
            <div>Multiplicator</div>
            <div>Șterge</div>
        </div>
        <div
            v-for="(game, index) in bonusBuyGames"
            :key="index"
            class="grid grid-cols-[30px_minmax(200px,_1fr)_120px_120px_120px_120px_40px] gap-2 mt-4"
        >
            <div
                class="w-8 h-8 justify-center self-center text-white bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:focus:ring-blue-800"
            >
                {{ index }}
            </div>
            <div>
                <input
                    @input="checkModifiedFields(game, index)"
                    v-model="game.name"
                    type="text"
                    id="name"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Game Name"
                />
            </div>
            <div>
                <input
                    @input="checkModifiedFields(game, index)"
                    v-model="game.stake"
                    type="number"
                    id="stake"
                    class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Stake"
                />
            </div>
            <div>
                <input
                    @input="checkModifiedFields(game, index)"
                    v-model="game.price"
                    type="number"
                    id="price"
                    class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Price"
                />
            </div>
            <div>
                <input
                    @input="checkModifiedFields(game, index)"
                    v-model="game.result"
                    type="number"
                    id="result"
                    class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Result"
                />
            </div>
            <div>
                <input
                    disabled
                    v-model="game.multiplier"
                    type="number"
                    id="multiplier"
                    class="cursor-not-allowed text-center disabled:opacity-75 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Multiplier"
                />
            </div>
            <button
                type="button"
                tabindex="-1"
                @click="removeBonusBuyGameRow(game.id)"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
            >
                <svg
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>
        <div class="flex mt-8 center justify-center">
            <button
                @click="createNewBonusBuyGameRow"
                type="button"
                class="w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
                + Game
            </button>
        </div>
    </template>
</template>
<script >
import axios from "axios";
export default {
    data() {
        return {
            bonusBuy: [],
            bonusBuyGames: [],
            timerUpdateBonusBuy: false,
            timerUpdateBonusBuyGames: false,
            updateListTimer: 5, //in seconds
        };
    },
    mounted() {
        this.getLatestList();
    },
    methods: {
        async getLatestList() {
            await axios
                .get("/api/bonus-buy")
                .then((response) => {
                    this.bonusBuy = response.data.bonusBuy;
                    this.bonusBuyGames = response.data.bonusBuyGames;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        checkModifiedFields(game, index) {
            this.calcCurentRowMultiplier(game, index);
            this.startTimerUpdateBonusBuyGames();
        },
        async resetBonusBuy() {
            await axios
                .post("/api/bonus-buy")
                .then((response) => {
                    this.bonusBuy = response.data.bonusBuy;
                    this.bonusBuyGames = response.data.bonusBuyGames;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //add new games row
        async createNewBonusBuyGameRow() {
            this.forceUpdateBonusBuyGames();
            let bonusBuyId = this.bonusBuy.id;
            await axios
                .post("/api/bonus-buy-games", {
                    bonusBuyId,
                })
                .then((response) => {
                    this.bonusBuyGames.push(response.data);
                })
                .catch((error) => {
                    console.log(error);
                });
            await this.getLatestList();
        },
        async forceUpdateBonusBuyGames() {
            let games = this.bonusBuyGames;
            await axios
                .put("/api/bonus-buy-games", {
                    games,
                })
                .then(function (response) {
                    //console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        //remove games row
        async removeBonusBuyGameRow(id) {
            await axios
                .delete("/api/bonus-buy-games/" + id)
                .then((response) => {
                    this.bonusBuyGames = this.bonusBuyGames.filter(
                        (game) => game.id != id
                    );
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //update games
        async updateBonusBuyGames() {
            let games = this.bonusBuyGames;
            await axios
                .put("/api/bonus-buy-games", {
                    games,
                })
                .then(function (response) {
                    //console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
            this.timerUpdateBonusBuyGames = false;
            await this.getLatestList();
        },
        //update list name
        async updateBonusBuy() {
            let bonusBuy = this.bonusBuy;
            await axios
                .patch("/api/bonus-buy", {
                    bonusBuy,
                })
                .then(function (response) {
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
            this.timerUpdateBonusBuy = false;
        },
        startTimerUpdateBonusBuy() {
            if (this.timerUpdateBonusBuy == true) {
                return;
            }
            this.timerUpdateBonusBuy = true;
            setTimeout(this.updateBonusBuy, 5000);
        },
        startTimerUpdateBonusBuyGames() {
            if (this.timerUpdateBonusBuyGames == true) {
                return;
            }
            this.timerUpdateBonusBuyGames = true;
            setTimeout(this.updateBonusBuyGames, 5000);
        },
        calcCurentRowMultiplier(game, index) {
            if (
                game.name == "" ||
                game.stake == "" ||
                game.price == "" ||
                game.result == ""
            ) {
                return;
            }
            this.bonusBuyGames[index].multiplier = Math.round(
                game.result / game.stake,
                2
            );
        },
    },
};
</script>
