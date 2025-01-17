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
          <div class="justify-end text-center">
            {{bonusBattleStage.name}} | In Progres
          </div>
          <div class="battle flex gap-2 align-middle items-center justify-center">
            <div>
              {{bonusBattleConcurrents[0].name}}
            </div>
            <div>
vs
            </div>
            <div>
              {{bonusBattleConcurrents[1].name}}
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
