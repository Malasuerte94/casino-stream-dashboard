<script>
import axios from "axios";
import Chart from "chart.js/auto";

export default {
  data() {
    return {
      loading: true,
      hunts: [],
    };
  },
  async mounted() {
    await this.fetchHunts();
    if (this.hunts.length) {
      this.renderBarChart();
      this.renderLineChart();
    } else {
      console.warn("No hunts available to display in the charts.");
    }
    this.loading = false;
  },
  methods: {
    async fetchHunts() {
      try {
        const response = await axios.get("/api/bonus-hunt-all");
        this.hunts = response.data.hunts || [];
      } catch (error) {
        console.error("Error fetching hunts:", error);
      }
    },
    renderBarChart() {
      const labels = this.hunts.map(hunt => this.formatDate(hunt.created_at));
      const startData = this.hunts.map(hunt => parseFloat(hunt.start));
      const resultData = this.hunts.map(hunt => parseFloat(hunt.result));
      const profitData = this.hunts.map(
          hunt => parseFloat(hunt.result) - parseFloat(hunt.start)
      );

      new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
          labels,
          datasets: [
            {
              label: "Cost",
              data: startData,
              backgroundColor: "rgb(239,0,0)",
            },
            {
              label: "Câștig",
              data: resultData,
              backgroundColor: "rgb(10,253,0)",
            },
            {
              label: "Profit",
              data: profitData,
              backgroundColor: profitData.map(value =>
                  value >= 0 ? "rgba(0,255,222,0.6)" : "rgba(27,27,27,0.6)"
              ),
            },
          ],
        },
        options: {
          plugins: {
            legend: {
              display: true,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      });
    },
    renderLineChart() {
      const labels = this.hunts.map(hunt => this.formatDate(hunt.created_at));
      const startData = this.hunts.map(hunt => parseFloat(hunt.start));
      const resultData = this.hunts.map(hunt => parseFloat(hunt.result));

      new Chart(document.getElementById("lineChart"), {
        type: "line",
        data: {
          labels,
          datasets: [
            {
              label: "Cost (Trend)",
              data: startData,
              borderColor: "rgb(239,0,0)",
              borderWidth: 2,
              fill: false,
            },
            {
              label: "Câștig (Trend)",
              data: resultData,
              borderColor: "rgb(10,253,0)",
              borderWidth: 2,
              fill: false,
            },
          ],
        },
        options: {
          plugins: {
            legend: {
              display: true,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      });
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      const day = date.getDate().toString().padStart(2, "0");
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const year = date.getFullYear();
      const hour = date.getHours().toString().padStart(2, "0");
      const minute = date.getMinutes().toString().padStart(2, "0");
      return `${day}-${month}-${year} ${hour}:${minute}`;
    },
    calculateTotal(field) {
      return this.hunts.reduce((sum, hunt) => {
        const value = parseFloat(hunt[field]);
        if (isNaN(value)) {
          console.warn(`Invalid ${field} value detected: ${hunt[field]}`);
          return sum;
        }
        return sum + value;
      }, 0);
    },
  },
  computed: {
    totalStart() {
      return this.calculateTotal("start");
    },
    totalResult() {
      return this.calculateTotal("result");
    },
    totalProfit() {
      return this.totalResult - this.totalStart;
    },
    profitClass() {
      return this.totalProfit >= 0 ? "text-green-500" : "text-red-500";
    },
    profitLabel() {
      const sign = this.totalProfit >= 0 ? "+" : "-";
      return `${sign}${Math.abs(this.totalProfit).toFixed(2)} lei`;
    },
  },
};
</script>

<template>
  <div class="p-6 mt-6 text-center">
    <h2 class="text-lg font-bold">BONUS HUNTS</h2>
  </div>
  <div>
    <!-- Bar Chart Section -->
    <div class="chart-container">
      <div class="chart"><canvas id="barChart"></canvas></div>
    </div>

    <!-- Line Chart Section -->
    <div class="chart-container mt-8">
      <div class="chart"><canvas id="lineChart"></canvas></div>
    </div>

    <!-- Totals Section -->
    <div class="totals p-6 mt-6" v-if="!loading">
      <div>Total Start: {{ totalStart }} lei</div>
      <div>Total Câștig: {{ totalResult }} lei</div>
      <div>
        Total Profit:
        <span :class="profitClass">{{ profitLabel }}</span>
      </div>
    </div>
  </div>
</template>

<style>
.chart-container {
  margin: 20px auto;
  max-width: 800px;
}
.text-green-500 {
  color: #32cd32; /* Green for profit */
  font-weight: bold;
}
.text-red-500 {
  color: #dc143c; /* Red for loss */
  font-weight: bold;
}
</style>
