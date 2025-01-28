<template>
  <template v-if="!loading">
    <div class="table" ref="viewport">
      <div class="table-container">
        <div class="header-list">
          <div class="header-list-title">
            <span class="img-list">
              <SvgBh/>
            </span>
            <span>Bonus Battle</span>
            <div class="bonus-details">
              <div class="list-cost">Miză <span>{{ bonusBattleInfo.stake }} lei</span></div>
              <div class="list-opened">
                <div class="details">
                  <span>{{ totalConcurrents }} Jocuri</span>
                </div>
              </div>
            </div>
          </div>
          <div class="header-details">
            <div>Scor<span>AVG: {{ avgScore }}</span></div>
            <div><span>TOP: {{ bestScore }}</span></div>
            <div>Rezultat<span :class="{ 'text-red-500': totalProfit < 0, 'text-green-500': totalProfit >= 0 }">{{ totalProfit }} lei
            </span></div>
          </div>
        </div>
        <div v-if="!battleWinner?.id">
          <div class="justify-end text-center uppercase text-sm px-3 py-3 stage font-bold">
            <span>{{ bonusBattleStage.name }}</span>
          </div>
          <div class="battle flex gap-2 align-middle items-center justify-center relative">
            <div
                v-for="(concurrent, index) in bonusBattleConcurrents"
                :key="concurrent?.id || index"
                class="w-100 grow flex-col flex gap-2 px-2 max-w-[190px]"
            >
              <div
                  :class="'concurrent flex rounded-md text-white '"
              >
                <img
                    :src="getGameThumbnail(concurrent.game.image)"
                    alt="Game Thumbnail"
                    class="w-100 rounded-lg"
                />
              </div>
              <div class="from-user mt-2">
                {{ concurrent?.for_user || 'N/A' }}
              </div>


                <table
                    class="table-auto mb-2 w-full text-sm border-collapse border border-gray-700 rounded-md overflow-hidden">
                  <thead>
                  <tr class="bg-black text-white uppercase text-xs">
                    <th class="border border-gray-700 px-2 py-2">Cost</th>
                    <th class="border border-gray-700 px-2 py-2">Rezultat</th>
                    <th class="border border-gray-700 px-2 py-2">Scor</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr
                      v-for="(score, scoreIndex) in getConcurrentScores(concurrent?.id, bonusBattleBracket?.id)"
                      :key="score.id || scoreIndex"
                      class="border-t border-gray-700 bg-gray-700"
                  >
                    <td class="border border-gray-700 px-2 py-1">{{ score.cost_buy }}</td>
                    <td class="border border-gray-700 px-2 py-1">{{ score.result_buy }}</td>
                    <td
                        class="border border-gray-700 px-2 py-1 font-bold"
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

        <div v-else class="w-full p-4 text-white rounded-lg shadow-lg flex space-x-4">
          <!-- Winner Card -->
          <div class="text-center w-40 flex flex-col justify-center">
            <img
                v-if="battleWinner.game.image"
                :src="getGameThumbnail(battleWinner.game.image)"
                alt="Game Thumbnail"
                class="w-100 auto mx-auto object-cover rounded-lg mb-4"
            />
          </div>

          <!-- Stats and Table -->
          <div class="flex-grow">
            <!-- Stats -->
            <div class="mb-4">
              <span class="text-md font-bold mb-2">Winner Winner!</span>
              <p class="text-md"><span class="font-bold">Total Scor:</span> {{ totalBalanceScore }}</p>
              <p class="text-md"><span class="font-bold">Pentru User:</span> {{ battleWinner.for_user || 'N/A' }}</p>
            </div>

            <!-- Battle Cost and Profit Table -->
            <div class="bg-gray-800 rounded-md p-2">
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

        <div class="bracket-container py-3 px-3">
          <div v-for="bracket in bonusBattleAllBracketsCurentStage" :key="bracket.id" class="bracket-item">
            <div
                :class="['participant', { loser: bracket.winner !== bracket.participant_a && bracket.winner !== 'N/A' }]"
            >
              {{ bracket.participant_a }}
            </div>
            <div class="vs">VS</div>
            <div
                :class="['participant', { loser: bracket.winner !== bracket.participant_b && bracket.winner !== 'N/A' }]"
            >
              {{ bracket.participant_b }}
            </div>
          </div>
        </div>

        <div class="shadow-md py-3 px-3">
          <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm text-gray-200">
              <tbody>
              <tr
                  v-for="(concurrent, index) in bonusBattleAllConcurrents"
                  :key="concurrent?.id"
                  :class="index % 2 === 0 ? 'bg-gray-900' : 'bg-gray-800'"
              >
                <td class="border border-black px-1 py-1">
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
    async updateTheListFromTimeToTime() {
      setInterval(async () => {
        if (!this.isUpdating) {
          this.isUpdating = true;
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


.footer-donate {
  display: none !important;
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

.vs-symbol {
  position: absolute;
  width: 100px;
  height: 100px;
  z-index: 2;
  align-items: center;
  align-self: baseline;
  top: 69px;
}

.concurrent {
  height: 230px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  z-index: 1;
  border: 2px black solid;
  line-height: 24px;
}

.stage {
  position: relative;

  span {
    background-color: #FFC400;
    padding: 0 10px;
    border-radius: 5px;
    color: black;
    z-index: 2;
    position: relative;
  }

  &::before {
    z-index: 1;
    content: "";
    position: absolute;
    height: 50px;
    background-color: trasparent;
    border-left: 2px solid black;
    border-right: 2px solid black;
    border-top: 2px solid black;
    width: 200px;
    left: 0;
    right: 0;
    margin: auto;
    top: 20px;
  }
}

.from-user {
  margin-top: -15px;
  padding-top: 5px;
  text-align: center;
  font-size: 16px;
  background-color: black;
  border-radius: 5px;
  margin-bottom: 15px;
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
