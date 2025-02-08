<template>
  <div class="box-winners" v-if="list_results">
    <div class="title">Rezultate Listă</div>
    <div class="box-list">
      <div class="row">
        <div>Rezultat</div>
        <div>{{list_results.result}} LEI</div>
      </div>
      <div class="row">
        <div>Cel mai mare X</div>
        <div>{{list_results.biggestMultiplier}} x</div>
      </div>
      <div class="row">
        <div>Cel mai mic X</div>
        <div>{{list_results.lowestMultiplier}} x</div>
      </div>
      <div class="row">
        <div>Joc Câștigător</div>
        <div>{{list_results.gameWinner}}</div>
      </div>
    </div>
    <div class="title">Câștigători</div>
    <div class="box-results">
      <div class="row-1">
        <div class="title">Rezultat</div>
        <div class="wiiner"><img class="avatar" :src="winner.resultWinner.user.profile_photo_path" alt="">{{winner.resultWinner.user.name}}</div>
        <div class="pick">{{winner.resultWinner.pick}} LEI</div>
      </div>
      <div class="row-2">
        <div class="title">Cel mai mare X</div>
        <div class="wiiner"><img class="avatar" :src="winner.biggestMultiplierWinner.user.profile_photo_path" alt="">{{winner.biggestMultiplierWinner.user.name}}</div>
        <div class="pick">{{winner.biggestMultiplierWinner.pick}} x</div>
      </div>
      <div class="row-3">
        <div class="title">Cel mai mic X</div>
        <div class="wiiner"><img class="avatar" :src="winner.lowestMultiplierWinner.user.profile_photo_path" alt="">{{winner.lowestMultiplierWinner.user.name}}</div>
        <div class="pick">{{winner.lowestMultiplierWinner.pick}} x</div>
      </div>
      <div class="row-4">
        <div class="title">Joc Câștigător</div>
        <div class="wiiner"><img class="avatar" :src="winner.gameWinnerWinner.user.profile_photo_path" alt="">{{winner.gameWinnerWinner.user.name}}</div>
        <div class="pick">{{list_results.gameWinner}}</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ["id"],
  data() {
    return {
      winner: null,
      list_results: null,
    };
  },
  async mounted() {
    await this.pollForWinner();
  },
  methods: {
    async pollForWinner() {
      this.winner = null;
      this.list_results = null;
      try {
        const response = await axios.get("/api/get-bonus-winner/" + this.id);
        if (response.status === 204) {
          setTimeout(this.pollForWinner, 5000);
        } else if (response.status === 200) {
          this.winner = response.data.winners;
          this.list_results = response.data.list_results;
          setTimeout(() => {
            this.winner = null;
            this.pollForWinner();
          }, 30000);
        }
      } catch (error) {
        console.error('Error polling for winner:', error);
      }
    },
  },
};
</script>

<style lang="scss">
.footer-donate {
  display: none !important;
}
.box-winners {
  font-family: Montserrat;
  width: 800px;
  height: 500px;
  position: absolute;
  margin: auto;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  .box-list {
    border-radius: 10px;
    padding: 20px;
    background-color: rgba(255, 163, 0, 0.63);
    font-size: 20px;
    text-transform: uppercase;
    font-weight: bold;
    margin-bottom: 20px;
    .row {
      color: white;
      border-bottom: 1px solid white;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      grid-template-rows: 1fr;
      grid-column-gap: 0px;
      grid-row-gap: 0px;
    }
  }
  .box-results {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-template-rows: 1fr;
    grid-column-gap: 30px;
    grid-row-gap: 0px;
    .row-1 {
      background-color: #ffb600;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 7px 6px #bd6900;
    }
    .row-2 {
      background-color: #8cff00;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 7px 6px #108c00;
    }
    .row-3 {
      background-color: #00f7ff;
      box-shadow: 7px 6px #00bda7;
      padding: 20px;
      border-radius: 10px;
    }
    .row-4 {
      background-color: #d000ff;
      box-shadow: 7px 6px #a400bd;
      padding: 20px;
      border-radius: 10px;
    }

  }
  .title {
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
  }
  .avatar {
    width: 70px;
    height: 70px;
    border-radius: 50px;
  }
  .wiiner {
    flex-direction: column;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .pick {
    margin-top: 10px;
    text-align: center;
  }
}
</style>
