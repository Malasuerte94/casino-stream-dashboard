<template>
  <template v-if="!loading">
    <div class="table" ref="viewport">
      <div class="table-container">
        <div class="header-list">
          <div class="header-list-title">
            <span class="img-list">
              <SvgBh />
            </span>
            <span>Bonus Battle</span>
            <div class="bonus-details">
              <div class="list-cost">Miză <span>{{ bonusBattleInfo.stake }} lei</span></div>
              <div class="list-opened">
                <div class="details">
                  <span>{{ totalConcurrents }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="header-details">
            <div>Avg Scor<span>1</span></div>
            <div>Top Scor<span>2</span></div>
            <div>Rezultat<span>3 lei</span></div>
          </div>
        </div>
        <div>
          <div class="justify-end text-center uppercase text-sm px-3 py-3">
            {{bonusBattleStage.name}}
          </div>
          <div class="battle flex gap-2 align-middle items-center justify-center relative">
            <div
                v-for="(concurrent, index) in bonusBattleConcurrents"
                :key="concurrent?.id || index"
                class="w-100 grow flex-col flex gap-2 px-2 max-w-[190px]"
            >
              <div
                  :class="'concurrent flex px-4 py-2 rounded-md text-white ' + (index === 0 ? 'bg-blue-500' : 'bg-red-500')"
              >
                {{ concurrent?.name || 'N/A' }}
              </div>
              <div class="from-user">
                {{ concurrent?.for_user || 'N/A' }}
              </div>
              <div>
                <table class="table-auto mb-2 w-full text-sm border-collapse border border-gray-700 rounded-md overflow-hidden">
                  <thead>
                  <tr class="bg-black text-white">
                    <th class="border border-gray-700 px-2 py-2">Cost</th>
                    <th class="border border-gray-700 px-2 py-2">Rezultat</th>
                    <th class="border border-gray-700 px-2 py-2">Scor</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr
                      v-for="(score, scoreIndex) in getConcurrentScores(concurrent?.id, bonusBattleStage?.id)"
                      :key="score.id || scoreIndex"
                      class="border-t border-gray-700 bg-gray-700"
                  >
                    <td class="border border-gray-700 px-2 py-2">{{ score.cost_buy }}</td>
                    <td class="border border-gray-700 px-2 py-2">{{ score.result_buy }}</td>
                    <td
                        class="border border-gray-700 px-2 py-2 font-bold"
                        :class="score.score < 1 ? 'text-red-500' : 'text-green-500'"
                    >
                      {{ score.score }}
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- VS Symbol -->
            <div class="vs-symbol flex justify-center items-center text-2xl">
              VS
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
  components: { SvgBh },
  props: ["id"],
  data() {
    return {
      loading: true,
      bonusBattleInfo: [],
      bonusBattleConcurrents: [],
      bonusBattleStage: [],
      bonusBattleScores: [],
      bonusBattleWinner: [],
      bonusBattleAllConcurrents: [],
      isUpdating: false,
    };
  },
  computed: {
    totalConcurrents() {
      return this.bonusBattleAllConcurrents.length;
    },
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
            this.bonusBattleStage = response.data.stage;
            this.bonusBattleScores = response.data.current_score;
            this.bonusBattleWinner = response.data.winner;
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
    getConcurrentScores(concurrentId, stageId) {
      if (!concurrentId || !stageId) return [];
      const filteredScores = [];
      for (let i = 0; i < this.bonusBattleScores.length; i++) {
        const score = this.bonusBattleScores[i];
        if (score.bonus_concurrent_id === concurrentId && score.bonus_stage_id === stageId) {
          filteredScores.push(score);
        }
      }
      return filteredScores;
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
  width: 50px;
  height: 50px;
  margin: auto;
  background-color: #ffc400;
  border-radius: 50px;
  color: black;
  font-family: "American Captain", sans-serif;
  font-size: 36px;
  box-shadow: 0 0 13px 4px #000000;
  z-index: 2;
}
.concurrent {
  height: 150px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  font-size: 24px;
  text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
  z-index: 1;
  border: 2px black solid;
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
      margin-right: 5px;// This is essential to create the 3D effect
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
