<template>
    <div
        class="grid grid-cols-[minmax(200px,_1fr)_120px_120px_120px] gap-2 mt-2 mb-2"
    >
        <input
            @input="startTimerUpdateBonusHunt"
            type="text"
            id="bonus_Hunt_name"
            v-model="bonusHunt.name"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Nume Lista"
        />
        <input
            @input="startTimerUpdateBonusHunt"
            type="number"
            id="bonus_hunt_start"
            v-model="bonusHunt.start"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Cost (LEI)"
        />
        <input
            disabled
            type="number"
            id="bonus_hunt_result"
            v-model="bonusHunt.result"
            class="cursor-not-allowed disabled:opacity-75 bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Rezultat (LEI)"
        />
        <button
            @click="resetBonusHunt"
            type="button"
            tabindex="-1"
            class="justify-center self-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
        >
            LISTA NOUA
        </button>
    </div>
    <template v-if="bonusHuntGames">
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
            <div>Rezultat (LEI)</div>
            <div>Multiplicator</div>
            <div>Șterge</div>
        </div>
        <div
            v-for="(game, index) in bonusHuntGames"
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
                    min="1"
                    type="number"
                    id="stake"
                    class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Stake"
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
                    class="cursor-not-allowed text-center disabled:opacity-75 bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Multiplier"
                />
            </div>
            <button
                type="button"
                tabindex="-1"
                @click="removeBonusHuntGameRow(game.id)"
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
                @click="createNewBonusHuntGameRow"
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
            bonusHunt: [],
            bonusHuntGames: [],
            timerUpdateBonusHuntGamesTimeout: null,
            timerUpdateBonusHuntTimeout: null,
            updateListTimer: 5, //in seconds
        };
    },
    mounted() {
        this.getLatestList();
    },
    methods: {
        async getLatestList() {
            await axios
                .get("/api/bonus-hunt")
                .then((response) => {
                    this.bonusHunt = response.data.bonusHunt;
                    this.bonusHuntGames = response.data.bonusHuntGames;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        checkModifiedFields(game, index) {
            this.calcCurentRowMultiplier(game, index);
            this.startTimerUpdateBonusHuntGames();
            this.startTimerUpdateBonusHunt();
        },
        async resetBonusHunt() {
            await axios
                .post("/api/bonus-hunt")
                .then((response) => {
                    this.bonusHunt = response.data.bonusHunt;
                    this.bonusHuntGames = response.data.bonusHuntGames;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //add new games row
        async createNewBonusHuntGameRow() {
            this.forceUpdateBonusHuntGames();
            let bonusHuntId = this.bonusHunt.id;
            await axios
                .post("/api/bonus-hunt-games", {
                    bonusHuntId,
                })
                .then((response) => {
                    this.bonusHuntGames.push(response.data);
                })
                .catch((error) => {
                    console.log(error);
                });
            await this.getLatestList();
        },
        async forceUpdateBonusHuntGames() {
            let games = this.bonusHuntGames;
            await axios
                .put("/api/bonus-hunt-games", {
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
        async removeBonusHuntGameRow(id) {
            await axios
                .delete("/api/bonus-hunt-games/" + id)
                .then((response) => {
                    this.bonusHuntGames = this.bonusHuntGames.filter(
                        (game) => game.id != id
                    );
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //update games
        async updateBonusHuntGames() {
            let games = this.bonusHuntGames;
            await axios
                .put("/api/bonus-hunt-games", {
                    games,
                })
                .then(function (response) {
                    //console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
            await this.getLatestList();
        },
        //update list name
        async updateBonusHunt() {
            let bonusHunt = this.bonusHunt;
            await axios
                .patch("/api/bonus-hunt", {
                    bonusHunt,
                })
                .then(function (response) {
                    //console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        startTimerUpdateBonusHunt() {
            if (this.timerUpdateBonusHuntTimeout) {
                clearTimeout(this.timerUpdateBonusHuntTimeout);
            }
            this.timerUpdateBonusHuntTimeout = setTimeout(
                this.updateBonusHunt,
                this.updateListTimer * 1000
            );
        },
        startTimerUpdateBonusHuntGames() {

            if (this.timerUpdateBonusHuntGamesTimeout) {
                clearTimeout(this.timerUpdateBonusHuntGamesTimeout);
            }
            this.timerUpdateBonusHuntGamesTimeout = setTimeout(
                this.updateBonusHuntGames,
                this.updateListTimer * 1000
            );

        },
        calcCurentRowMultiplier(game, index) {
            if (game.name == "" || game.stake == "" || game.result < 0) {
                return;
            }
            this.bonusHuntGames[index].multiplier = game.result == 0 ? 0 : Math.round(game.result / game.stake, 1);
            this.bonusHuntGames[index].result = Math.ceil(game.result);
        },
    },
};
</script>
