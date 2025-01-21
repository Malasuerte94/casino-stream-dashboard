<template>
  <template v-if="!loading">
    <div class="schedule-weekly-container flex gap-6 bg-transparent p-4 rounded-lg shadow-lg">
      <!-- Existing Weekly Schedule Table -->
      <div class="weekly-table w-1/2">
        <div v-if="weeklySchedules.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
              v-for="schedule in weeklySchedules"
              :key="schedule.id"
              class="schedule-card bg-gray-900 p-4 rounded-lg shadow-md border border-gray-700"
          >
            <h3 class="text-md font-semibold text-gray-200">Program</h3>
            <ul class="mt-4 space-y-2">
              <li
                  v-for="day in schedule.days"
                  :key="day.id"
                  class="flex justify-between items-center p-2 rounded shadow-sm border border-gray-600"
                  :class="{
                  'bg-[#FFC400] text-gray-900': isCurrentDay(day.date), // Highlight current day
                  'opacity-50 line-through text-white': !day.active, // Strike-through canceled days
                  'bg-gray-800': !isCurrentDay(day.date), // Default background
                }"
              >
                <span class="text-lg font-bold">{{ formatDayName(day.date) }}</span>
                <span class="text-lg">{{ day.info }}</span>
              </li>
            </ul>
          </div>
        </div>
        <p v-else class="text-gray-400">No schedules for this week.</p>
      </div>

      <!-- New Table: Rows of 4 Days -->
      <div class="program-table w-1/2">
        <table class="w-full text-sm text-gray-200 border-collapse">
          <tbody>
          <tr v-for="(dayGroup, index) in groupedDays" :key="index">
            <td
                v-for="day in dayGroup"
                :key="day.id"
                class="p-1 border border-transparent"
            >
              <!-- Day Card -->
              <div
                  :class="{
            'bg-[#FFC400] text-gray-900': isCurrentDay(day.date),
            'bg-gray-800 opacity-75 line-through': !day.active,
            'bg-gray-900': day.active && !isCurrentDay(day.date),
          }"
                  class="p-2 rounded-lg shadow-md text-left"
              >
                <div class="font-bold text-lg mb-1">
                  {{ formatDayName(day.date) }}
                </div>
                <div class="text-sm">
                  {{ day.info }}
                </div>
              </div>
            </td>
          </tr>
          </tbody>
        </table>

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
      weeklySchedules: [],
      groupedDays: [],
    };
  },
  async mounted() {
    await this.fetchWeeklySchedules();
    this.loading = false;
    this.groupDaysIntoRows();
  },
  methods: {
    async fetchWeeklySchedules() {
      try {
        const response = await axios.get(`/api/schedule/weekly/${this.id}`);
        this.weeklySchedules = response.data;
      } catch (error) {
        console.error("Error fetching weekly schedules:", error);
      }
    },
    formatDayName(date) {
      const days = [
        "Duminică",
        "Luni",
        "Marți",
        "Miercuri",
        "Joi",
        "Vineri",
        "Sâmbătă",
      ];
      const dayIndex = new Date(date).getDay();
      return days[dayIndex];
    },
    isCurrentDay(date) {
      const today = new Date().toISOString().split("T")[0];
      return date === today;
    },
    groupDaysIntoRows() {
      const allDays = this.weeklySchedules.flatMap((schedule) => schedule.days);
      const grouped = [];
      for (let i = 0; i < allDays.length; i += 4) {
        grouped.push(allDays.slice(i, i + 4));
      }
      this.groupedDays = grouped;
    },
  },
};
</script>

<style scoped>
.schedule-weekly-container {
  display: flex;
  flex-direction: row;
  gap: 1.5rem;
}
.program-table {
  max-height: 100%;
  overflow-y: auto;
}
</style>
