<template>
    <div class="text-white">
        {{ stream.casino }} - {{ stream.deposit }} LEI
    </div>
</template>

<script>
export default {
    data() {
        return {
            stream: [],
        }
    },
    async mounted() {
        await this.getLatestStream()
        this.updateTheStreamFromTimeToTime()
    },
    methods: {

        async getLatestStream() {
            await axios.get('/api/stream')
                .then(response => {
                    this.stream = response.data.stream

                })
                .catch(error => {
                    console.log(error)
                })
        },
        async updateTheStreamFromTimeToTime() {
            setInterval(async () => {
                await this.getLatestStream()
            }, 5000)
        }
    }
}
</script>
<style>
body {
    background-color: black;
    font-size: 24px;
    font-weight: bold;
}
</style>