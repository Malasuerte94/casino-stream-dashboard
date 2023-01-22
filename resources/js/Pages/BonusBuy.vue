<template>
    <div v-for="(game, index) in bonusBuyGames" :key="index" class="tabel">
        <div>{{ game.name }}</div>
        <div>{{ game.stake }}</div>
        <div>{{ game.price }}</div>
        <div>{{ game.result }}</div>
        <div>x{{ game.multiplier }}</div>
    </div>
</template>
<script >
export default {
    data() {
        return {
            bonusBuy: [],
            bonusBuyGames: [],
        }
    },
    mounted() {
        this.getLatestList()
    },
    methods: {
        async getLatestList() {
            await axios.get('/api/bonus-buy')
                .then(response => {
                    this.bonusBuy = response.data.bonusBuy
                    this.bonusBuyGames = response.data.bonusBuyGames

                })
                .catch(error => {
                    console.log(error)
                })
        },
    }
}
</script>
<style>
.tabel {
    display: grid;
    grid-template-columns: 3fr 1fr 1fr 1fr 1fr;
    grid-gap: 10px;
    margin-bottom: 10px;
}
</style>