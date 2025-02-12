<template>
  <template v-if="!loading">
    <div class="table-list" :style="{
          'border': settings.borderEnabled ? settings.borderWidth + 'px ' + settings.borderColor + ' solid' : 'unset',
        }" ref="viewport">
      <div class="table-container">
        <div class="header-list">
          <div class="header-list-title" :style="{
                'background-color': settings.headerBgColor,
                'color': settings.headerFontColor,
                'font-size': settings.headerFontSize + 'px',
              }">
            <span class="img-list" v-if="settings.showIcon">
              <SvgBh/>
            </span>
            <span>Bonus</span>
            <span>{{ huntOrBuy }}</span>
            <div class="bonus-details">
              <div class="list-cost" :style="{'background-color': settings.headerCellBgColor}">Cost <span>{{
                  startAmount
                }} {{ settings.currency }}</span></div>
              <div class="list-opened" :style="{'background-color': settings.headerCellBgColor}">
                <div class="details">
                  <span>{{ gamesOpenedNr }}/</span>
                </div>
                <div class="details">
                  <span>{{ gamesTotalNr }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="progress" :style="{
                'height': settings.progressBarHeight + 'px'
              }">
            <div
                class="progress-bar-fill"
                :style="{ width: progressPercentage + '%', 'background': 'linear-gradient(90deg, '+ settings.progressBarLow +' 0%, '+ settings.progressBarHigh +' 100%)' }"
            ></div>
          </div>
          <div class="header-details" :style="{
                'background-color': settings.subheaderBgColor,
                'color': settings.subheaderFontColor,
                'font-size': settings.subheaderFontSize + 'px'
              }">
            <div class="flex flex-col text-center">Req (x)<span>{{ requiredAverageX }}</span></div>
            <div class="flex flex-col text-center">Avg (x)<span>{{ averageMulti }}</span></div>
            <div class="flex flex-col text-center">Top (x)<span>{{ gameHighestMulti }}</span></div>
            <div class="flex flex-col text-center">Top
              (plata)<span>{{ gameHighestResult }} {{ settings.currency }}</span></div>
            <div class="flex flex-col text-center">Rezultat<span>{{ bonusList.result }} {{ settings.currency }}</span>
            </div>
          </div>
        </div>

        <!-- Buy Section -->
        <template v-if="huntOrBuy === 'Buy'">
          <div class="header" :style="{
              'background-color': settings.tableHeaderBgColor,
              'color': settings.tableHeaderFontColor,
              'font-size': settings.tableHeaderFontSize + 'px'
            }">
            <div class="header-content_buy header-content">
              <div class="text-center">#</div>
              <div>Joc</div>
              <div>Miză</div>
              <div>Preț</div>
              <div>Plată</div>
              <div>Multi</div>
            </div>
          </div>
          <div class="scroll-wrapper" ref="scrollWrapper" :style="{'background-color': settings.tableBgColor}">
            <div class="games" :style="{
                'color': settings.tableBodyFontColor,
                'font-size': settings.tableBodyFontSize + 'px'
              }" ref="gamesList">
              <div
                  v-for="(game, index) in duplicatedGames"
                  :key="'a-' + index"
                  class="game-single row-game_buy"
                  :style="{
                  'border-bottom': '1px solid ' + settings.tableDividerColor,
                  'background-color': firstEmptyResultId === game.id ? settings.tableCurrentGameColor : 'unset'
                }"
                  :class="{ 'current': firstEmptyResultId === game.id }"
              >
                <div class="number_game">{{ index % bonusListGames.length + 1 }}</div>
                <div class="name_game">{{  game.game?.name ?? 'N/A' }}</div>
                <div class="stake">{{ game.stake }}</div>
                <div class="result">{{ game.price === '0' || game.price == null ? '' : game.price }}</div>
                <div class="result">{{ game.result === '0' || game.result == null ? '' : game.result }}</div>
                <div class="multi">{{
                    game.multiplier === '0' || game.multiplier == null ? '' : 'x' + game.multiplier
                  }}
                </div>
              </div>
            </div>
          </div>
        </template>

        <!-- Hunt Section -->
        <template v-else>
          <div class="header" :style="{
              'background-color': settings.tableHeaderBgColor,
              'color': settings.tableHeaderFontColor,
              'font-size': settings.tableHeaderFontSize + 'px'
            }">
            <div class="header-content_hunt header-content">
              <div class="text-center">#</div>
              <div>Joc</div>
              <div>Miză</div>
              <div>Plată</div>
              <div>Multi</div>
            </div>
          </div>
          <div class="scroll-wrapper" ref="scrollWrapperHunt" :style="{'background-color': settings.tableBgColor}">
            <div class="games" :style="{
                'color': settings.tableBodyFontColor,
                'font-size': settings.tableBodyFontSize + 'px'
              }" ref="gamesListHunt">
              <div
                  v-for="(game, index) in duplicatedGames"
                  :key="'b-' + index"
                  class="game-single row-game_hunt"
                  :style="{
                  'border-bottom': '1px solid ' + settings.tableDividerColor
                }"
                  :class="{ 'current': firstEmptyResultId === game.id }"
              >
                <div class="number_game">{{ index % bonusListGames.length + 1 }}</div>
                <div class="name_game">{{ game.game?.name ?? 'N/A' }}</div>
                <div class="stake">{{ game.stake }}</div>
                <div>{{ game.result === '0' || game.result == null ? '' : game.result }}</div>
                <div>{{ game.multiplier === '0' || game.multiplier == null ? '' : 'x' + game.multiplier }}</div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </template>
</template>

<script>
import SvgBh from "@/Components/MainComponents/SvgBh.vue";

export default {
  components: {SvgBh},
  props: ["id"],
  data() {
    return {
      loading: true,
      bonusList: [],
      bonusListGames: [],
      allSettings: null,
      settings: {},
      isUpdating: false,
      scrollSpeed: 500,
      itWasDuplicated: true
    };
  },
  computed: {
    startAmount() {
      return this.bonusList.start ?? 0;
    },
    huntOrBuy() {
      return this.allSettings?.bonus_list === "buy" ? "Buy" : "Hunt";
    },
    duplicatedGames() {
      if (this.shouldScroll()) {
        this.itWasDuplicated = true;
        return [...this.bonusListGames, ...this.bonusListGames];
      } else {
        this.itWasDuplicated = false;
        return this.bonusListGames
      }
    },
    firstEmptyResultId() {
      const game = this.bonusListGames.find((game) => game.result === '0');
      return game ? game.id : null;
    },
    gamesOpenedNr() {
      return this.bonusListGames.filter(
          (game) => game.result && game.result !== '0'
      ).length;
    },
    gamesTotalNr() {
      return this.bonusListGames.length;
    },
    gameHighestMulti() {
      const multipliers = this.bonusListGames
          .map((game) => {
            return game?.multiplier ? parseFloat(game.multiplier.trim()) : NaN;
          })
          .filter((value) => !isNaN(value));
      return multipliers.length ? Math.max(...multipliers) : 0;
    },
    gameHighestResult() {
      const validResults = this.bonusListGames
          .filter(game => game.result && game.result !== "0")
          .map(game => parseFloat(game.result));

      if (validResults.length === 0) return 0;
      return Math.max(...validResults);
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
          0
      );
      return Math.round(totalMultiplier / validGames.length);
    },
    requiredAverageX() {
      const listCost = this.bonusList.start; // Total cost to break even
      const totalPayout = this.bonusListGames.reduce(
          (sum, game) => sum + (game.result ? parseFloat(game.result) : 0),
          0
      );
      const remainingCost = Math.max(listCost - totalPayout, 0);
      const remainingGames = this.bonusListGames.filter(
          (game) => !game.result || game.result === "0"
      ).length;
      if (remainingGames === 0) return 0;
      const costPerGame = remainingCost / remainingGames;

      let totalRequiredX = 0;

      this.bonusListGames.forEach((game) => {
        const gameStake = parseFloat(game.stake);

        if (game.result && game.result !== "0") {
          return;
        }
        const requiredX = costPerGame / gameStake;
        totalRequiredX += requiredX;
      });

      return Math.round(totalRequiredX / remainingGames);
    }
  },
  async mounted() {
    window.Echo.channel(`App.Models.User.${this.id}`)
        .listen('BonusBuyOrHuntUpdated', async () => {
          await this.updateList();
        })
        .listen('SettingsUpdated', async () => {
          await this.updateList();
        });

    await this.getSettings();
    await this.getLatestList();
    this.loading = false;

    await this.$nextTick(() => {
      this.initAutoScroll(this.$refs.scrollWrapper);
      this.initAutoScroll(this.$refs.scrollWrapperHunt);
    });
  },
  methods: {
    shouldScroll() {
      const container = this.$refs.scrollWrapper || this.$refs.scrollWrapperHunt;
      const viewport = this.$refs.viewport;
      if (!container || !viewport) return false;
      const header = document.querySelector('.header-list');
      const headerHeight = header ? header.getBoundingClientRect().height : 0;
      const viewportRect = viewport.getBoundingClientRect();
      const availableHeight = viewportRect.bottom - headerHeight;
      const gameItems = container.querySelectorAll('.game-single');
      if (gameItems.length === 0) return false;
      let totalHeight = 0;
      gameItems.forEach(item => {
        totalHeight += item.getBoundingClientRect().height;
      });
      if (this.itWasDuplicated) {
        totalHeight = totalHeight / 2
      }
      return totalHeight + 20 > availableHeight;
    },
    async getSettings() {
      await axios
          .get("/api/settings/" + this.id)
          .then((response) => {
            this.allSettings = response.data.settings;
            this.loadSettings()
          })
          .catch((error) => {
            console.log(error);
          });
    },
    async loadSettings() {
      this.settings = JSON.parse(this.allSettings.obs_bonus_list);
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
    async updateList() {
      if (!this.isUpdating) {
        this.isUpdating = true;
        await this.getSettings();
        await this.getLatestList();
        this.isUpdating = false;
        this.initAutoScroll();
      }
    },
    initAutoScroll() {
      const gamesList = this.$refs.gamesList || this.$refs.gamesListHunt;
      if (!gamesList) return;
      if (!this.shouldScroll()) {
        gamesList.classList.remove('scrolling');
      } else {
        gamesList.classList.add('scrolling');
      }
    }
  },
};
</script>

<style lang="scss">
.scroll-wrapper {
  overflow: hidden;
  position: relative;
  max-height: 100vh;
}

.games {
  display: flex;
  flex-direction: column;

  &.scrolling {
    animation: scrollAnimation 15s linear infinite;
  }
}

@keyframes scrollAnimation {
  0% {
    transform: translateY(0);
  }
  100% {
    transform: translateY(-50%);
  }
}

.footer-donate {
  display: none !important;
}

html,
body,
#app {
  //background-image: url(https://gratisography.com/wp-content/uploads/2025/01/gratisography-dog-vacation-800x525.jpg);
  font-size: 1.3rem;
  width: 400px;
  height: 100vh;
  background-color: transparent !important;
}

.table-list {
  width: 100%;
  color: white;
  border: 4px black solid;
  border-radius: 5px;
  max-height: 100vh;
  display: block;
}

.header {
  position: relative;
  z-index: 1;
  background-color: #454545;

  .header-content {
    font-size: 16px;
    text-transform: uppercase;
    font-weight: bold;
    box-shadow: 0px -4px 20px 0px black;
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
    color: #8d8d8d;
    font-size: 14px;
    padding: 5px;
  }

  .stake {
    padding-left: 10px;
  }

  .current {
    background-color: rgba(255, 166, 0, 0.329);

    .number_game {
      color: #ffc400;
    }

    .name_game:before {
      display: none;
    }
  }
}

.header-list {
  z-index: 999;
  position: relative;

  .header-list-title {
    display: flex;
    flex-direction: row;
    gap: 5px;
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
      perspective: 1000px;
      margin-right: 5px; // This is essential to create the 3D effect
      svg {
        width: 20px; // Adjust width and height for better visibility
        height: 20px;
        animation: spin 10s infinite ease-in-out;
        transform-style: preserve-3d;
        position: relative;

        &::before {
          transform: translateZ(-5px); // Back side shadow
        }

        &::after {
          transform: translateZ(5px); // Front side shadow
        }
      }
    }

    .bonus-details {
      margin-left: auto;
      display: flex;
      gap: 5px;

      .list-opened {
        margin-left: auto;
        display: flex;
        background: #ffffffbf;
        padding: 0 5px;
        border-radius: 5px;
      }

      .list-cost {
        background: #ffffffbf;
        padding: 0 5px;
        border-radius: 5px;
      }
    }
  }

  .progress {
    position: relative;
    display: flex;
    justify-content: space-evenly;
    font-size: 14px;
    height: 5px;
    background-color: rgb(110 110 110);

    .details {
      z-index: 2;
      padding: 5px;
      color: #ffffff;
      text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
    }

    .progress-bar-fill {
      position: absolute;
      background: linear-gradient(90deg, rgba(255, 196, 0, 1) 0%, rgba(255, 109, 0, 1) 100%);
      border-bottom-right-radius: 10px;
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
      padding: 0 5px;
      border: 1px solid #3a3a3a;
      border-radius: 4px;
      font-size: 16px;
      font-weight: bold;
    }
  }

  .header-details {
    background: black;
    display: flex;
    justify-content: space-evenly;
    font-size: 16px;
    padding: 5px 0px;
  }
}
</style>
