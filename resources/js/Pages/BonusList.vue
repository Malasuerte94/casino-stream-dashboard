<template>
    <template v-if="!loading">
        <div class="table" ref="viewport">
            <div class="table-container">
                <div class="header-list">
                    <div class="header-list-title">
                        <span>Bonus</span>
                        <span class="img-list">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                version="1.1"
                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                xmlns:svgjs="http://svgjs.com/svgjs"
                                width="50"
                                height="50"
                                x="0"
                                y="0"
                                viewBox="0 0 365.725 365.725"
                                style="enable-background: new 0 0 512 512"
                                xml:space="preserve"
                                class=""
                            >
                                <g>
                                    <path
                                        d="M357.29 315.48c-.11-.111-.22-.222-.331-.331l-14.08-13.76-25.68-25.84-19.04-19.04 16.88-16.88a8 8 0 0 0 0-11.28l-18.24-18.24a8 8 0 0 0-11.28 0l-6.56 6.56-26.96-30.16 106.88-95.68a8.002 8.002 0 0 0 2.64-5.6l3.84-76.72a8 8 0 0 0-2.32-6.08 8 8 0 0 0-6.08-2.32l-76.72 3.76a8.002 8.002 0 0 0-5.6 2.64l-91.76 102.88L90.959 6.748a8.002 8.002 0 0 0-5.6-2.64L8.639.348a8 8 0 0 0-6.08 2.32 8 8 0 0 0-2.32 6.08l3.84 76.72a8.002 8.002 0 0 0 2.64 5.6l107.04 96-26.88 29.68-6.56-6.56a8 8 0 0 0-11.28 0l-18.24 18.24a8 8 0 0 0 0 11.28l16.88 16.88-19.12 18.96-40 40c-11.443 11.488-11.407 30.078.081 41.521a29.365 29.365 0 0 0 20.639 8.559 29.122 29.122 0 0 0 20.72-8.56l58.72-58.72 16.88 16.88a8 8 0 0 0 11.28 0l18.24-18.24a8 8 0 0 0 0-11.28l-6.24-6.96 34-30.32 34 30.32-6.56 6.56a8 8 0 0 0 0 11.28l18.24 18.24a8 8 0 0 0 11.28 0l16.88-16.88 19.04 19.04 25.76 25.76 13.92 13.92c11.374 11.557 29.963 11.705 41.52.331 11.557-11.373 11.705-29.963.331-41.519zm-89.851-87.452-14.08 14.08-30.08-30.08 16.56-14.8 27.6 30.8zm-248-146.96-3.2-64.56 64.56 3.2 90.88 101.6-18.56 20.72-33.6-33.6a8 8 0 0 0-11.28 11.28l34.64 34.32-18.4 20.72-105.04-93.68zm19.12 264a13.28 13.28 0 0 1-9.44 3.92c-7.358-.546-12.881-6.954-12.334-14.312a13.361 13.361 0 0 1 3.374-7.928l8-8 18.72 18.48-8.32 7.84zm19.52-19.52-18.4-18.48 14.48-14.48 18.88 18.88-14.96 14.08zm25.84-25.84-18.88-18.88 13.84-12.96 18.88 18.88-13.84 12.96zm47.2-2.16-16.8-16.8-30.16-30.16-16.48-16.56 6.88-6.88 64 64-7.44 6.4zm6-30.48-14.08-14.08 133.6-133.6a8 8 0 1 0-11.28-11.28l-133.6 133.6-14.08-14.08 32.8-36.64 154.08-171.2 64.56-3.2-3.2 64.56-110.56 98.8-98.24 87.12zm57.28-29.84 17.2-14.48 30.64 30.64-14.08 14.08-33.76-30.24zm56 43.44-16.24 17.28-6.88-6.88 64-64 6.88 6.88-16.88 16.88-30.88 29.84zm16.96 5.68 19.52-18.48 13.36 13.36-18.88 18.88-14-13.76zm39.2 39.2-14.48-14.48 18.88-18.88 14.48 14.48-18.88 18.88zm39.04 19.92-.64-.4c-5.285 5.039-13.595 5.039-18.88 0l-8-8 19.28-18.32 8 8c5.17 5.131 5.276 13.458.24 18.72z"
                                        fill="#000000"
                                        data-original="#000000"
                                    ></path>
                                </g>
                            </svg>
                        </span>
                        <span>{{
                            settings.bonus_list == "buy" ? "Buy" : "Hunt"
                        }}</span>
                    </div>
                    <div class="progress">
                        <div
                            class="progress-bar-fill"
                            :style="{ width: progressPercentage + '%' }"
                        ></div>
                        <div class="details">
                            DESCHISE<span>{{ gamesOpenedNr }}</span>
                        </div>
                        <div class="details">
                            TOTAL<span>{{ gamesTotalNr }}</span>
                        </div>
                        <div class="details">
                            RĂMASE<span>{{ gamesRemainingNr }}</span>
                        </div>
                    </div>
                    <div class="header-details">
                        <div>
                            Avg (x)<span>{{ averageMulti }}</span>
                        </div>
                        <div>
                            Top (x)<span>{{ gameHighestMulti }}</span>
                        </div>
                        <div>
                            Cost<span>{{ bonusList.start }} lei</span>
                        </div>
                        <div>
                            Rezultat<span>{{ bonusList.result }} lei</span>
                        </div>
                    </div>
                </div>
                <template v-if="settings.bonus_list == 'buy'">
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
                    <div
                        class="games scroll-wrapper"
                        :style="bonusListGames.length > 10 ? {
                            animationDuration: animationTime + 's',
                            animationName: 'scrollAnimation',
                            animationTimingFunction: 'linear',
                            animationIterationCount: 'infinite',
                            '--translate-y': transformY + 'px',
                        } : {}"
                    >
                        <div
                            v-for="(game, index) in bonusListGames"
                            :key="'a-' + game.id"
                            class="game-single row-game_buy"
                            :class="[
                                index === firstEmptyResultIndex
                                    ? 'current'
                                    : '',
                            ]"
                        >
                            <div class="number_game">{{ index + 1 }}</div>
                            <div>{{ game.name }}</div>
                            <div>{{ game.stake }}</div>
                            <div>{{ game.price }}</div>
                            <div>
                                {{
                                    game.result == 0 || game.result == null
                                        ? ""
                                        : "x" + game.result
                                }}
                            </div>
                            <div>
                                {{
                                    game.multiplier == 0 ||
                                    game.multiplier == null
                                        ? ""
                                        : "x" + game.multiplier
                                }}
                            </div>
                        </div>
                        <template v-if="bonusListGames.length > 10">
                            <div
                                v-for="(game, index) in bonusListGames"
                                :key="'b-' + game.id"
                                class="game-single row-game_buy"
                                :class="[
                                    index === firstEmptyResultIndex
                                        ? 'current'
                                        : '',
                                ]"
                            >
                                <div class="number_game">{{ index + 1 }}</div>
                                <div>{{ game.name }}</div>
                                <div>{{ game.stake }}</div>
                                <div>{{ game.price }}</div>
                                <div>
                                    {{
                                        game.result == 0 || game.result == null
                                            ? ""
                                            : "x" + game.result
                                    }}
                                </div>
                                <div>
                                    {{
                                        game.multiplier == 0 ||
                                        game.multiplier == null
                                            ? ""
                                            : "x" + game.multiplier
                                    }}
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template v-else>
                    <div class="header">
                        <div class="header-content_hunt header-content">
                            <div class="text-center">#</div>
                            <div>Joc</div>
                            <div>Miză</div>
                            <div>Plată</div>
                            <div>Multi</div>
                        </div>
                    </div>
                    <div
                        class="games scroll-wrapper"
                        :style="bonusListGames.length > 10 ? {
                            animationDuration: animationTime + 's',
                            animationName: 'scrollAnimation',
                            animationTimingFunction: 'linear',
                            animationIterationCount: 'infinite',
                            '--translate-y': transformY + 'px',
                        } : {}"
                    >
                        <div
                            v-for="(game, index) in bonusListGames"
                            :key="'a-' + game.id"
                            class="game-single row-game_hunt"
                            :class="[
                                index === firstEmptyResultIndex
                                    ? 'current'
                                    : '',
                            ]"
                        >
                            <div class="number_game">{{ index + 1 }}</div>
                            <div>{{ game.name }}</div>
                            <div>{{ game.stake }}</div>
                            <div>
                                {{
                                    game.result == 0 || game.result == null
                                        ? ""
                                        : game.result
                                }}
                            </div>
                            <div>
                                {{
                                    game.multiplier == 0 ||
                                    game.multiplier == null
                                        ? ""
                                        : "x" + game.multiplier
                                }}
                            </div>
                        </div>
                        <template v-if="bonusListGames.length > 10">
                            <div
                                v-for="(game, index) in bonusListGames"
                                :key="'b-' + game.id"
                                class="game-single row-game_hunt"
                                :class="[
                                    index === firstEmptyResultIndex
                                        ? 'current'
                                        : '',
                                ]"
                            >
                                <div class="number_game">{{ index + 1 }}</div>
                                <div>{{ game.name }}</div>
                                <div>{{ game.stake }}</div>
                                <div>
                                    {{
                                        game.result == 0 || game.result == null
                                            ? ""
                                            : game.result
                                    }}
                                </div>
                                <div>
                                    {{
                                        game.multiplier == 0 ||
                                        game.multiplier == null
                                            ? ""
                                            : "x" + game.multiplier
                                    }}
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </template>
</template>
<script>
export default {
    props: ["id"],
    data() {
        return {
            loading: true,
            bonusList: [],
            bonusListGames: [],
            settings: null,
            isUpdating: false,
            isAnimating: false,
        };
    },
    async mounted() {
        await this.getSettings();
        await this.getLatestList();
        this.updateTheListFromTimeToTime();
        this.loading = false;
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
                if (!this.isAnimating) {
                    this.isUpdating = true;
                    await this.getLatestList();
                    await this.getSettings();
                    this.isUpdating = false;
                }
            }, 8000);
        },
    },
    computed: {
        firstEmptyResultIndex() {
            return this.bonusListGames.findIndex((game) => game.result == 0);
        },
        gamesOpenedNr() {
            return this.bonusListGames.filter(
                (game) => game.result && game.result != 0
            ).length;
        },
        gamesTotalNr() {
            return this.bonusListGames.length;
        },
        gamesRemainingNr() {
            return this.bonusListGames.filter(
                (game) => !game.result || game.result == 0
            ).length;
        },
        gameHighestMulti() {
            return Math.max(
                ...this.bonusListGames.map((game) =>
                    parseFloat(game.multiplier)
                )
            );
        },
        progressPercentage() {
            return (this.gamesOpenedNr / this.gamesTotalNr) * 100;
        },
        averageMulti() {
            const validGames = this.bonusListGames.filter(
                (game) => game.result !== null && game.result !== "0"
            );
            if (validGames.length === 0) return 0;
            const totalMultiplier = validGames.reduce(
                (sum, game) => sum + parseFloat(game.multiplier),
                2
            );
            const average = totalMultiplier / validGames.length;
            return Math.round(average);
        },
        animationTime() {
            const timePerGame = 0.5;
            const animationTimeForOneList =
                timePerGame * this.bonusListGames.length;
            return 2 * animationTimeForOneList;
        },
        transformY() {
            //inaltime joc care poate fi loata direct si e ami ok
            const gamesListHeight = 32;
            return -((gamesListHeight * (this.bonusListGames.length * 2)) / 2);
        },
    },
};
</script>

<style lang="scss">

.footer-donate {
  display: none !important;
}

.scroll-wrapper {
    overflow: hidden;
    height: 100vh;
}

.games {
    backface-visibility: hidden;
    will-change: transform;
}

.games:hover {
    animation-play-state: paused;
}

@keyframes scrollAnimation {
    0% {
        transform: translate3d(0, 0, 0);
    }
    100% {
        transform: translate3d(0, var(--translate-y), 0);
    }
}
body,
#app {
    font-size: 1.3rem;
    width: 400px;
    height: 100vh;
}

.table {
    width: 100%;
    background-color: #000000ba;
    color: white;
    border: 4px black solid;
    border-radius: 5px;
    max-height: 100vh;
    display: block;
    overflow: hidden;
}

.header {
    position: relative;
    z-index: 1;
    background-color: #454545;
    .header-content {
        font-size: 16px;
        text-transform: uppercase;
        font-weight: bold;
    }
    .header-content_buy {
        display: grid;
        grid-template-columns: 30px 115px 50px 70px 70px 60px;
    }
    .header-content_hunt {
        display: grid;
        grid-template-columns: 30px 155px 60px 80px 70px;
    }
}

.games {
    box-sizing: border-box;
    font-size: 16px;
    overflow: inherit;
    .game-single {
        display: grid;
        border-bottom: 1px solid #d5d5d53b;
        align-content: center;
        align-items: center;
    }
    .row-game_buy {
        grid-template-columns: 30px 115px 50px 70px 70px 60px;
    }
    .row-game_hunt {
        grid-template-columns: 30px 155px 60px 80px 70px;
    }
    .number_game {
        color: rgb(109, 109, 109);
        font-size: 14px;
        padding: 5px;
    }
    .current {
        background-color: rgba(255, 166, 0, 0.329);
    }
}

.header-list {
    z-index: 999;
    position: relative;
    .header-list-title {
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 20px;
        align-items: center;
        text-transform: uppercase;
        font-weight: bold;
        font-size: 16px;
        padding: 10px;
        background: #ffc400;
        border-bottom: 2px black solid;
        color: black;
        .img-list {
            display: block;
            svg {
                width: 20px;
                height: 20px;
            }
        }
    }
    .progress {
        position: relative;
        display: flex;
        justify-content: space-evenly;
        font-size: 14px;
        background-color: rgb(110 110 110);
        .details {
            z-index: 2;
            padding: 5px;
            color: black;
        }
        .progress-bar-fill {
            position: absolute;
            position: absolute;
            background-color: rgb(255, 196, 0);
            top: 0;
            bottom: 0;
            z-index: 1;
            left: 0;
        }
    }
    .progress,
    .header-details {
        span {
            margin-left: 5px;
            padding: 1px 2px;
            border: 1px solid #3a3a3a;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    }
    .header-details {
        background: black;
        display: flex;
        justify-content: space-evenly;
        font-size: 14px;
        padding: 5px 0px;
    }
}
</style>
