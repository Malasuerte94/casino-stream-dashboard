<template>
    <div class="chart"><canvas id="activity"></canvas></div>
    <div class="totals p-6">
            <div>Total Depozit: {{ totalDeposits }} lei</div>
            <div>Total Retrageri: {{ totalWithdrawals }} lei</div>
    </div>
</template>
<script>
import axios from "axios";
import Chart from 'chart.js/auto'

export default {
    components: {
        Chart
    },
    data() {
        return {
            deposits: [],
            withdrawals: [],
            chartData:[]
        };
    },
    async mounted() {
        await this.getDeposits();
        await this.getWithdrawals();

        let that = this
        new Chart(
        document.getElementById('activity'),
        {
            type: 'line',
            data: {
            labels: that.deposits.map(row => that.formatDateLabels(row.created_at)),
            datasets: [
                {
                label: 'Depuneri',
                data: that.deposits.map(row => row.amount),
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
                },
                {
                label: 'Retrageri',
                data: that.withdrawals.map(row => row.amount),
                fill: false,
                borderColor: 'rgb(180, 20, 55)',
                tension: 0.1
                }
            ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                        useBorderRadius: true,
                        borderRadius: 0.5,
                        boxWidth: 8,
                        boxHeight: 2
                        }
                    }
                }
            }
        }
        )
    },
    methods: {
        async getDeposits() {
            await axios
                .get("/api/deposits")
                .then((response) => {
                    this.deposits = response.data.deposits;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async getWithdrawals() {
            await axios
                .get("/api/withdrawals")
                .then((response) => {
                    this.withdrawals = response.data.withdrawals;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        formatDateLabels(dateRaw) {
            const date = new Date(dateRaw);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is 0-indexed
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
    },
    computed: {
        totalDeposits() {
            return this.deposits.reduce((sum, deposit) => {
                const value = parseFloat(deposit.amount);
                if (isNaN(value)) {
                    console.warn(`Invalid deposit value detected: ${deposit.amount}`);
                    return sum;
                }
                return sum + value;
            }, 0);
        },
        totalWithdrawals() {
            return this.withdrawals.reduce((sum, withdrawal) => {
                const value = parseFloat(withdrawal.amount);
                if (isNaN(value)) {
                    console.warn(`Invalid withdrawal value detected: ${withdrawal.amount}`);
                    return sum;
                }
                return sum + value;
            }, 0);
        }
    },
};
</script>
