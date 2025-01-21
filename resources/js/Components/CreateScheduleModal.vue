<template>
  <div
      class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50"
  >
    <div class="modal bg-white w-full max-w-lg p-6 rounded-lg shadow-lg">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Create Schedule</h2>
      <form @submit.prevent="submitSchedule" class="space-y-4">
        <!-- Date Range Inputs -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-gray-700" for="startDate">Start Date</label>
            <input
                type="date"
                id="startDate"
                v-model="startDate"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700" for="endDate">End Date</label>
            <input
                type="date"
                id="endDate"
                v-model="endDate"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>
        </div>

        <!-- Announce to Discord Checkbox -->
        <div class="flex items-center">
          <input
              type="checkbox"
              id="announceToDiscord"
              v-model="announceToDiscord"
              class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
          <label for="announceToDiscord" class="ml-2 block text-sm text-gray-700">
            Announce to Discord
          </label>
        </div>

        <!-- Generate Days -->
        <button
            type="button"
            @click="generateDays"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-500 focus:outline-none focus:ring focus:ring-indigo-300"
        >
          Generate Days
        </button>

        <!-- Days Inputs -->
        <div class="grid grid-cols-[0.5fr_2fr] gap-4 border-t pt-4" v-for="(day, index) in days" :key="index">
          <span class="text-gray-700 text-sm font-medium flex items-center justify-center truncate">
            {{ formatDayName(day.date) }}
          </span>
          <input
              type="text"
              v-model="day.info"
              placeholder="Info"
              class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block"
          />
        </div>

        <!-- Submit Button -->
        <div class="flex gap-2">
          <button
              type="submit"
              class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-500 focus:outline-none focus:ring focus:ring-indigo-300"
          >
            Save Schedule
          </button>
          <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md shadow hover:bg-gray-200"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      startDate: '',
      endDate: '',
      days: [],
      announceToDiscord: true, // Default to true
    };
  },
  methods: {
    generateDays() {
      const start = new Date(this.startDate);
      const end = new Date(this.endDate);
      this.days = [];

      for (let d = start; d <= end; d.setDate(d.getDate() + 1)) {
        this.days.push({ date: new Date(d).toISOString().split('T')[0], info: '' });
      }
    },
    formatDayName(date) {
      const days = ['Duminică', 'Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă'];
      return days[new Date(date).getDay()];
    },
    submitSchedule() {
      axios
          .post('/api/schedule', {
            days: this.days,
            user_id: 1,
            announceToDiscord: this.announceToDiscord,
          })
          .then(() => {
            this.$emit('close');
          });
    },
  },
};
</script>
