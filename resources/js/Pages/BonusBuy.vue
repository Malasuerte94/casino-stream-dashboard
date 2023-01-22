<template>
    <div class="table-container">
        <div class="header">
            <div>#</div>
            <div>Joc</div>
            <div>Miza</div>
            <div>Pret</div>
            <div>Rezultat</div>
            <div>Multi</div>
        </div>
        <div class="slider" v-if="bonusBuyGames.length > 1">
        <template v-for="(game, index) in bonusBuyGames" :key="index">
            <div class="table" v-if="game.name">
                <div>{{ index + 1 }}</div>
                <div>{{ game.name }}</div>
                <div>{{ game.stake }}</div>
                <div>{{ game.price }}</div>
                <div>{{ game.result }}</div>
                <div>x{{ game.multiplier }}</div>
            </div>
        </template>
            
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            bonusBuy: [],
            bonusBuyGames: [],
        }
    },
    async mounted() {
        await this.getLatestList()
        this.updateTheListFromTimeToTime()
    },
    methods: {
        updateTableScroll() { 
            var viewHeight = window.innerHeight;
            var slider = this.$el.querySelector(".slider");
            var time = (slider.offsetHeight * 2.0 + viewHeight * 2) / 150.0; // 500px / sec
            slider.style.animationDuration = time + "s";
        },
        async getLatestList() {
            await axios.get('/api/bonus-buy')
                .then(response => {
                    this.bonusBuy = response.data.bonusBuy
                    this.bonusBuyGames = response.data.bonusBuyGames

                })
                .catch(error => {
                    console.log(error)
                })
            this.updateTableScroll()
        },
        async updateTheListFromTimeToTime() {
            setInterval(async () => {
                await this.getLatestList()
            }, 5000)
        }
    }
}
</script>

<style>
body {
    background-color: black;
    font-size: 24px;
}
.table {
    color: white;
    display: grid;
    grid-template-columns: 30px 3fr 1fr 1fr 1fr 1fr;
    grid-gap: 10px;
    margin-bottom: 10px;
    position: relative;
    text-align: left;
}

.header {
    font-size: 22px;
    width: 100vw;
    z-index: 1;
    background-color: black;
    padding: 5px;
    font-weight: bold;
    color: white;
    display: grid;
    grid-template-columns: 10px 3fr 1fr 1fr 1fr 1fr;
    grid-gap: 10px;
    margin-bottom: 10px;
    position: sticky;
}


.slider {
    position: absolute;
    list-style-type: none;
    text-align: center;
    animation: slider linear infinite;
}

.slider > div { line-height: 50px; width: 100vw; } 

@keyframes slider {
    0%   { transform: translateY(100vh) }
    100% { transform: translateY(-100%) }
}

</style>
