<template>
    <template v-if="!loading">
        <div v-if="settings.bonus_list == 'buy'" class="table">
            <div class="table-container">
                <div class="header">
                    <div class="header-content_buy header-content">
                        <div class="text-center">#</div>
                        <div>Joc</div>
                        <div>Miză</div>
                        <div>Preț</div>
                        <div>Plată</div>
                        <div>Multi</div>
                    </div>
                </div>
                <div class="games">
                    <div
                        class="game-single row-game_buy"
                        v-for="(game, index) in bonusListGames"
                        :key="index"
                    >
                        <div class="number_game">{{ index + 1 }}</div>
                        <div>{{ game.name }}</div>
                        <div>{{ game.stake }}</div>
                        <div>{{ game.price }}</div>
                        <div>{{ game.result }}</div>
                        <div>x{{ game.multiplier }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="table">
            <div class="table-container">
                <div class="header">
                    <div class="header-content_hunt header-content">
                        <div class="text-center">#</div>
                        <div>Joc</div>
                        <div>Miză</div>
                        <div>Plată</div>
                        <div>Multi</div>
                    </div>
                </div>
                <div class="games">
                    <div
                        class="game-single row-game_hunt"
                        v-for="(game, index) in bonusListGames"
                        :key="index"
                    >
                        <div class="number_game">{{ index + 1 }}</div>
                        <div>{{ game.name }}</div>
                        <div>{{ game.stake }}</div>
                        <div>{{ game.result }}</div>
                        <div>x{{ game.multiplier }}</div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</template>
<script>
import $ from "jquery";
export default {
    props: ["id"],
    data() {
        return {
            loading: true,
            bonusList: [],
            bonusListGames: [],
            settings: null,
        };
    },
    async mounted() {
        await this.getSettings();
        await this.getLatestList();
        this.updateTheListFromTimeToTime();
        this.loading = false;
        this.animation();
    },
    methods: {
        async getSettings() {
            await axios
                .get("/api/settings/" + this.id)
                .then((response) => {
                    this.settings = response.data.settings;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        async getLatestList() {
            await axios
                .get("/api/bonus-list/" + this.id)
                .then((response) => {
                    this.bonusList = response.data.bonusList;
                    this.bonusListGames = response.data.bonusListGames;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async updateTheListFromTimeToTime() {
            setInterval(async () => {
                await this.getLatestList();
                await this.getSettings();
            }, 5000);
        },
        animation() {
            setInterval(async () => {
                if(this.bonusListGames.length >= 10) {
                var marginHeight = $(".game-single").height();
                $(".games")
                    .stop(true, true)
                    .animate(
                        { marginTop: "-" + marginHeight + "px" },
                        1000,
                        function () {
                            $(this).children("div:first").appendTo(this);
                            $(this).css({ marginTop: 0 });
                        }
                );
                    }
            }, 3000);
        },
    },
};
</script>

<style>
body,
#app {
    font-size: 1.3rem;
    overflow: hidden;
    width: 400px;
    height: 100vh;
}

.table {
    width: 100%;
    height: 100%;
}

.header {
    height: 40px;
    position: relative;
    z-index: 1;
}
.header-content_buy {
    display: grid;
    grid-template-columns: 30px 115px 50px 70px 70px 60px;
}

.row-game_buy {
    padding-top: 10px;
    padding-bottom: 10px;
    display: grid;
    grid-template-columns: 30px 115px 50px 70px 70px 60px;
    border-bottom: 1px solid #d5d5d53b;
}

.header-content_hunt {
    display: grid;
    grid-template-columns: 30px 155px 60px 80px 70px;
}

.row-game_hunt {
    padding-top: 10px;
    padding-bottom: 10px;
    display: grid;
    grid-template-columns: 30px 155px 60px 80px 70px;
    border-bottom: 1px solid #d5d5d53b;
}

.games {
    top: 0;
    position: relative;
    box-sizing: border-box;
}
.number_game {
    color: rgb(109, 109, 109);
}
</style>
