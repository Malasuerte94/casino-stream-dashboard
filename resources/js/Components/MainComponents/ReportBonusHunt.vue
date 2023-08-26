<template>
    <div class="chart"><canvas id="activityHunt"></canvas></div>
    <div class="totals p-6" v-if="!loading">
        <div>Total Start: {{ totalStart }} lei</div>
        <div>Total Câștig: {{ totalResult }} lei</div>
    </div>
</template>
<script>
import axios from "axios";
import Chart from "chart.js/auto";

export default {
    components: {
        Chart,
    },
    data() {
        return {
            loading: true,
            hunts: [],
            chartData: [],
        };
    },
    async mounted() {
        await this.getAllHunts();
        let that = this;
        new Chart(document.getElementById("activityHunt"), {
            type: "line",
            data: {
                labels: that.hunts.map((row) =>
                    that.formatDateLabels(row.created_at)
                ),
                datasets: [
                    {
                        label: "Cost",
                        data: that.hunts.map((row) => row.start),
                        fill: false,
                        borderColor: "rgb(75, 192, 192)",
                        tension: 0.1,
                    },
                    {
                        label: "Câștig",
                        data: that.hunts.map((row) => row.result),
                        fill: false,
                        borderColor: "rgb(180, 20, 55)",
                        tension: 0.1,
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            useBorderRadius: true,
                            borderRadius: 0.5,
                            boxWidth: 8,
                            boxHeight: 2,
                        },
                    },
                },
            },
        });
        this.loading = false;
    },
    methods: {
        async getAllHunts() {
            await axios
                .get("/api/bonus-hunt-all")
                .then((response) => {
                    this.hunts = response.data.hunts;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        formatDateLabels(dateRaw) {
            const date = new Date(dateRaw);
            const day = date.getDate().toString().padStart(2, "0");
            const month = (date.getMonth() + 1).toString().padStart(2, "0");
            const year = date.getFullYear();
            const hour = date.getHours().toString().padStart(2, "0");
            const minute = date.getMinutes().toString().padStart(2, "0");

            return `${day}-${month}-${year} ${hour}:${minute}`;
        },
    },
    computed: {
        totalStart() {
            return this.hunts.reduce((sum, hunt) => {
                const value = parseFloat(hunt.start);
                if (isNaN(value)) {
                    console.warn(`Invalid start value detected: ${hunt.start}`);
                    return sum;
                }
                return sum + value;
            }, 0);
        },
        totalResult() {
            return this.hunts.reduce((sum, hunt) => {
                const value = parseFloat(hunt.result);
                if (isNaN(value)) {
                    console.warn(
                        `Invalid result value detected: ${hunt.result}`
                    );
                    return sum;
                }
                return sum + value;
            }, 0);
        },
    },
};
</script>
