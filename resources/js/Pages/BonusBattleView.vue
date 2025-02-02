<template>
  <template v-if="!loading">
    <div class="table-list" ref="viewport" :style="{
          'background-color': settings.tableBgColor,
          'border': settings.borderEnabled ? settings.borderWidth + 'px ' + settings.borderColor + ' solid' : 'unset',
        }">
      <div class="table-container">
        <div class="header-list">
          <div class="header-list-title" :style="{
                'background-color': settings.headerBgColor,
                'color': settings.headerFontColor,
                'font-size': settings.headerFontSize + 'px',
              }">
            <span>{{ bonusBattleInfo.title }}</span>
            <div class="bonus-details">
              <div class="list-cost">Miză <span>{{ bonusBattleInfo.stake }} {{ settings.currency }}</span></div>
              <div class="list-opened">
                <div class="details">
                  <span>{{ totalConcurrents }} Jocuri</span>
                </div>
              </div>
            </div>
          </div>
          <div class="header-details" :style="{
                'background-color': settings.subheaderBgColor,
                'color': settings.subheaderFontColor,
                'font-size': settings.subheaderFontSize + 'px',
              }">
            <div class="text-center uppercase text-sm stage font-bold flex justify-center items-center" :style="{
                  'background-color': settings.subheaderBgColorRound,
                  'color': settings.subheaderFontColorRound,
                }">
              {{ bonusBattleStage.name }}
            </div>
            <div><span>AVG: {{ avgScore }}</span></div>
            <div><span>TOP: {{ bestScore }}</span></div>
            <div><span :class="{ 'text-red-500': totalProfit < 0, 'text-green-500': totalProfit >= 0 }">{{
                totalProfit
              }} {{ settings.currency }}
            </span></div>
          </div>
        </div>
        <div v-if="!battleWinner?.id" class="main-battle">
          <div class="battle flex gap-2 align-middle items-center justify-center relative">
            <div
                v-for="(concurrent, index) in bonusBattleConcurrents"
                :key="concurrent?.id || index"
                class="w-100 grow flex-col flex gap-2 px-2 max-w-[190px]"
            >

              <div class="flex flex-col gap-2 mt-2">
                <div
                    :class="'concurrent flex rounded-md text-white '"
                >
                  <img
                      :src="getGameThumbnail(concurrent.game.image)"
                      alt="Game Thumbnail"
                      class="w-[100px] rounded-lg"
                  />
                </div>
                <div class="from-user" v-if="concurrent?.for_user !== null" :style="{
                  'background-color': settings.tableParticipantBgColor,
                  'color': settings.tableParticipantFontColor,
                  'font-size': settings.tableParticipantFontSize + 'px',
                }">
                  {{ concurrent?.for_user || 'N/A' }}
                </div>
              </div>


              <table
                  v-if="getConcurrentScores(concurrent?.id, bonusBattleBracket?.id).length > 0"
                  :style="{
                  'color': settings.tableScoresFontColor,
                  'font-size': settings.tableScoresFontSize + 'px',
                }"
                  class="table-auto mb-2 w-full border-collapse border border-gray-700 rounded-md overflow-hidden">
                <thead>
                <tr class="bg-black text-white uppercase" :style="{
                  'background-color': settings.tableScoresLabelBgColor,
                }">
                  <th class="border border-gray-700 px-1 py-1">Cost</th>
                  <th class="border border-gray-700 px-1 py-1">Rezultat</th>
                  <th class="border border-gray-700 px-1 py-1">Scor</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(score, scoreIndex) in getConcurrentScores(concurrent?.id, bonusBattleBracket?.id)"
                    :key="score.id || scoreIndex"
                    :style="{
                  'background-color': settings.tableScoresBgColor
                }"
                >
                  <td class="px-2 py-1">{{ score.cost_buy }}</td>
                  <td class="px-2 py-1">{{ score.result_buy }}</td>
                  <td
                      class="px-2 py-1 font-bold"
                      :class="score.score < 1 ? 'text-red-500' : 'text-green-500'"
                  >
                    {{ score.score.toFixed(2) }}
                  </td>
                </tr>
                </tbody>
              </table>

            </div>

            <!-- VS Symbol -->
            <div class="vs-symbol flex justify-center items-center text-2xl">
              <img :src="this.vsImageUrl" alt="vs"/>
            </div>
          </div>
        </div>

        <div v-else class="w-full p-4 text-white shadow-lg flex space-x-4 winner-battle">
          <!-- Winner Card -->
          <div class="text-center w-40 flex flex-col justify-center">
            <img
                v-if="battleWinner.game.image"
                :src="getGameThumbnail(battleWinner.game.image)"
                alt="Game Thumbnail"
                class="w-100 auto mx-auto object-cover rounded-lg"
            />
          </div>

          <!-- Stats and Table -->
          <div class="flex-grow">
            <!-- Stats -->
            <div class="mb-4" :style="{
                'font-size': (settings.headerFontSize * 1.2) + 'px',
              }">
              <span class="font-bold mb-2">Winner Winner!</span>
              <p><span class="font-bold">Total Scor:</span> {{ totalBalanceScore }}</p>
              <p><span class="font-bold">Pentru User:</span> {{ battleWinner.for_user || 'N/A' }}</p>
            </div>

            <!-- Battle Cost and Profit Table -->
            <div class="bg-gray-800 rounded-md p-2" :style="{
                'background-color': settings.headerBgColor,
                'color': settings.headerFontColor,
                'font-size': settings.headerFontSize + 'px',
              }">
              <table class="w-full text-sm text-gray-200">
                <thead>
                <tr>
                  <th class="text-left p-1 border-b border-gray-700">Battle Info</th>
                  <th class="text-right p-1 border-b border-gray-700">LEI</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                  <td class="p-1">Battle Cost</td>
                  <td class="p-1 text-right">{{ totalCost }}</td>
                </tr>
                <tr>
                  <td class="p-1">Battle Profit</td>
                  <td
                      class="p-1 text-right"
                      :class="{ 'text-green-400': totalProfit > 0, 'text-red-400': totalProfit < 0 }"
                  >
                    {{ totalProfit }}
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="second-battle">
          <div class="bracket-container py-2 px-2" v-if="bonusBattleAllBracketsCurentStage.length > 0">
            <div v-for="bracket in bonusBattleAllBracketsCurentStage" :key="bracket.id" class="bracket-item">
              <div
                  :class="['participant', { loser: bracket.winner !== bracket.participant_a && bracket.winner !== 'N/A' }]"
              >
                {{ bracket.participant_a }} - {{ bracket.participant_a_score }}
              </div>
              <div class="vs">VS</div>
              <div
                  :class="['participant', { loser: bracket.winner !== bracket.participant_b && bracket.winner !== 'N/A' }]"
              >
                {{ bracket.participant_b }} - {{ bracket.participant_b_score }}
              </div>
            </div>
          </div>

          <div class="shadow-md py-2 px-2">
            <div class="overflow-x-auto">
              <table class="table-auto w-full text-gray-200" :style="{
                  'font-size': settings.tableBodyFontSize + 'px'
                }">
                <tbody>
                <tr
                    v-for="(concurrent, index) in bonusBattleAllConcurrents"
                    :key="concurrent?.id"
                    :style="{
                  'background-color': index % 2 === 0 ? settings.tableHistoryBgOddColor : settings.tableHistoryBgEvenColor
                }"
                >
                  <td class="border border-black px-1 py-1 shrink w-0">
                    {{ concurrent?.is_eliminated ? '❌' : '✅' }}
                  </td>
                  <td class="border border-black px-2 py-1">
                    {{ concurrent?.game.name || 'N/A' }}
                  </td>
                  <td class="border border-black px-2 py-1">
                    {{ concurrent?.for_user || 'N/A' }}
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
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
      settings: {},
      bonusBattleInfo: [],
      bonusBattleConcurrents: [],
      bonusBattleStage: [],
      bonusBattleBracket: [],
      bonusBattleScores: [],
      bonusBattleWinner: [],
      bonusBattleAllConcurrents: [],
      bonusBattleAllBracketsCurentStage: [],
      battleWinner: [],
      avgScore: 0,
      bestScore: 0,
      totalProfit: 0,
      totalCost: 0,
      isUpdating: false,
      vsImageUrl: `/storage/assets/images/vs.gif`
    };
  },
  computed: {
    totalConcurrents() {
      return this.bonusBattleAllConcurrents.length;
    },
    totalBalanceScore() {
      return ((this.totalCost + this.totalProfit) / this.totalCost).toFixed(2);
    }
  },
  async mounted() {
    await this.getSettings();
    await this.getActiveBonusBattle();
    this.loading = false;
    await this.updateTheListFromTimeToTime();
  },
  methods: {
    async getActiveBonusBattle() {
      await axios
          .get("/api/bonus-battle-info/" + this.id)
          .then((response) => {
            this.bonusBattleInfo = response.data.battle;
            this.bonusBattleConcurrents = response.data.concurrents;
            this.bonusBattleAllConcurrents = response.data.all_concurrents;
            this.bonusBattleAllBracketsCurentStage = response.data.stage_brackets;
            this.bonusBattleStage = response.data.stage;
            this.bonusBattleBracket = response.data.bracket;
            this.bonusBattleScores = response.data.current_score;
            this.bonusBattleWinner = response.data.winner;
            this.battleWinner = response.data.battle_winner;
            this.avgScore = response.data.avg_score.toFixed(2);
            this.bestScore = response.data.best_score.toFixed(2);
            this.totalProfit = response.data.total_profit;
            this.totalCost = response.data.total_cost;
          })
          .catch((error) => {
            console.log(error);
          });
    },
    async getSettings() {
      let settingName = "obs_bonus_battle";
      try {
        const response = await axios.get(`/api/get-setting-public`, {
          params: {setting_name: settingName, user_id: this.id},
        });
        if (response.data.setting_value) {
          this.settings = JSON.parse(response.data.setting_value);
        }
        console.log(this.settings);
      } catch (error) {
        console.error(`Error loading setting "${settingName}":`, error);
        this.error = `Failed to load setting: ${settingName}`;
      }
    },
    async updateTheListFromTimeToTime() {
      setInterval(async () => {
        if (!this.isUpdating) {
          this.isUpdating = true;
          await this.getSettings();
          await this.getActiveBonusBattle();
          this.isUpdating = false;
        }
      }, 8000);
    },
    getConcurrentScores(concurrentId, bracketId) {
      if (!concurrentId || !bracketId) return [];
      const filteredScores = [];
      for (let i = 0; i < this.bonusBattleScores.length; i++) {
        const score = this.bonusBattleScores[i];
        if (score.bonus_concurrent_id === concurrentId && score.bracket_id === bracketId) {
          filteredScores.push(score);
        }
      }
      return filteredScores;
    },
    getGameThumbnail(imageName) {
      return imageName
          ? `/storage/games/${imageName}`
          : '';
    }
  },
};
</script>
<style lang="scss">
.footer-donate {
  display: none !important;
}

body,
#app {
  //background-image: url(https://gratisography.com/wp-content/uploads/2025/01/gratisography-dog-vacation-800x525.jpg);
  font-size: 1.3rem;
  width: 400px;
  height: 100vh;
}

.bracket-container {
  display: flex;
  flex-direction: column;
  gap: 5px;
  border-radius: 5px;
  color: #ffffff;
  font-family: Arial, sans-serif;

  .bracket-item {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    padding: 5px;
    background-color: black;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  .participant {
    font-size: 14px;
    font-weight: bold;
    transition: opacity 0.3s, text-decoration 0.3s;
  }

  .participant.loser {
    text-decoration: line-through;
    opacity: 0.5;
  }

  .vs {
    font-size: 10px;
    font-weight: bold;
    color: #ff5722;
  }
}

.table-list {
  width: 100%;
  border: 4px black solid;
  border-radius: 5px;
  max-height: 100vh;
  display: block;
  overflow: hidden;
}

.vs-symbol {
  position: absolute;
  width: 100px;
  height: 100px;
  z-index: 2;
  align-items: center;
  align-self: baseline;
  top: 24px;
}

.concurrent {
  height: auto;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  z-index: 1;
}

.from-user {
  text-align: center;
  font-size: 14px;
  background-color: black;
  border-radius: 5px;
}

.stage {
  background-color: #FFC400;
  padding: 0 10px;
  border-radius: 5px;
  color: black;
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
    padding: 10px;
    border-bottom: 2px black solid;

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
        background: rgb(255 255 255 / 27%);
        padding: 0 5px;
        border-radius: 5px;
      }

      .list-cost {
        background: rgb(255 255 255 / 27%);
        padding: 0 5px;
        border-radius: 5px;
      }
    }
  }

  .header-details {
    background: black;
    display: flex;
    justify-content: space-between;
    padding: 5px 5px;

    span {
      margin-left: 5px;
      padding: 0 5px;
      border: 1px solid #3a3a3a;
      border-radius: 4px;
      font-weight: bold;
    }
  }
}
</style>