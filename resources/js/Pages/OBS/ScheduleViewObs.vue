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
              :style="{'background-color': settings.cellBg, 'color': settings.cellFontColor}"
          >
            <h3 class="text-md font-semibold text-gray-200">Program</h3>
            <ul class="mt-4 space-y-2">
              <li
                  v-for="day in schedule.days"
                  :key="day.id"
                  class="flex justify-between items-center p-2 rounded shadow-sm border border-gray-600"
                  :class="{
                  'opacity-50 line-through text-white': !day.active,
                }"
                  :style="{'background-color': !isCurrentDay(day.date) ? settings.cellBgShort : settings.cellBgActive}"
              >
                <span class="text-lg font-bold" :style="{'font-size': settings.cellTitleFontSize + 'px'}">{{ formatDayName(day.date) }}</span>
                <span class="text-lg" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">{{ day.info }}</span>
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
                  :style="{'background-color': !isCurrentDay(day.date)? settings.cellBgShort : settings.cellBgActive, 'color': settings.cellFontColor}"
                  class="p-2 rounded-lg shadow-md text-left"
              >
                <div class="font-bold text-lg mb-1" :style="{'font-size': settings.cellTitleFontSize + 'px'}">
                  {{ formatDayName(day.date) }}
                </div>
                <div class="text-sm" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">
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
      settings: {},
    };
  },
  async mounted() {
    await this.getSettings();
    await this.fetchWeeklySchedules();
    this.loading = false;
    this.groupDaysIntoRows();
  },
  methods: {
    async getSettings() {
      let settingName = "obs_schedule";
      try {
        this.loading = true;
        const response = await axios.get(`/api/get-setting-public`, {
          params: {setting_name: settingName, user_id: this.id},
        });
        if (response.data.setting_value) {
          this.settings = JSON.parse(response.data.setting_value);
        }
        console.log(this.settings);
      } catch (error) {
        console.error(`Error loading setting "${settingName}":`, error);
        this.error = `Failed to load setting: ${settingName}`;
      } finally {
        this.loading = false;
      }
    },
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

<style>
html,
body,
#app {
  background-color: transparent !important;
}
.footer-donate {
  display: none !important;
}
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
