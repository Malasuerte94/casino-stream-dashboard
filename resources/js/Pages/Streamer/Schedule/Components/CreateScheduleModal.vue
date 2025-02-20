<template>
  <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50 transition-all duration-300">
    <div class="modal bg-gray-900 w-full max-w-lg p-6 rounded-md shadow-lg transition-all duration-300">
      <h2 class="text-xl font-semibold text-gray-100 mb-4">Create Schedule</h2>
      <form @submit.prevent="submitSchedule" class="space-y-4">
        <!-- Date Range Inputs -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="startDate" class="block text-sm font-medium text-gray-300">Start Date</label>
            <flat-pickr
                v-model="startDate"
                :config="flatpickrConfig"
                class="mt-1 block w-full border border-gray-600 rounded-md shadow-sm bg-gray-900 text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
            />
          </div>
          <div>
            <label for="endDate" class="block text-sm font-medium text-gray-300">End Date</label>
            <flat-pickr
                v-model="endDate"
                :config="flatpickrConfig"
                class="mt-1 block w-full border border-gray-600 rounded-md shadow-sm bg-gray-900 text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
            />
          </div>
        </div>

        <!-- Announce to Discord Checkbox -->
        <div class="flex items-center">
          <input
              type="checkbox"
              id="announceToDiscord"
              v-model="announceToDiscord"
              class="h-4 w-4 text-indigo-600 border-gray-600 rounded focus:ring-indigo-500 transition-all duration-300"
          />
          <label for="announceToDiscord" class="ml-2 block text-sm text-gray-300">
            Announce to Discord
          </label>
        </div>

        <!-- Generate Days Button -->
        <button
            type="button"
            @click="generateDays"
            class="btn-primary"
        >
          Generate Days
        </button>

        <!-- Days Inputs -->
        <div class="grid grid-cols-[0.5fr_2fr] gap-4 border-t border-gray-700 pt-4" v-for="(day, index) in days" :key="index">
          <span class="text-gray-300 text-sm font-medium flex items-center justify-center truncate">
            {{ formatDayName(day.date) }}
          </span>
          <input
              type="text"
              v-model="day.info"
              placeholder="Info"
              class="input-primary"
          />
        </div>

        <!-- Submit & Cancel Buttons -->
        <div class="flex gap-2">
          <button
              type="submit"
              class="w-full btn-primary"
          >
            Save Schedule
          </button>
          <button
              type="button"
              @click="$emit('close')"
              class="btn-secondary"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { Romanian } from 'flatpickr/dist/l10n/ro.js';

export default {
  components: {
    flatPickr: FlatPickr,
  },
  data() {
    return {
      startDate: '',
      endDate: '',
      days: [],
      announceToDiscord: true, // Default to true
      flatpickrConfig: {
        locale: Romanian,
        weekNumbers: true,
        firstDayOfWeek: 1,
        dateFormat: 'Y-m-d',
      },
    };
  },
  methods: {
    generateDays() {
      const start = new Date(this.startDate);
      const end = new Date(this.endDate);
      this.days = [];
      for (let d = start; d <= end; d.setDate(d.getDate() + 1)) {
        this.days.push({date: new Date(d).toISOString().split('T')[0], info: ''});
      }
    },
    formatDayName(date) {
      const days = ['Duminică', 'Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă'];
      return days[new Date(date).getDay()];
    },
    submitSchedule() {
      axios.post('/api/schedule', {
        days: this.days,
        user_id: 1,
        announceToDiscord: this.announceToDiscord,
      }).then(() => {
        this.$emit('close');
      });
    },
  },
};
</script>

<style scoped>
/* Custom styles for dark mode */
/* You can add additional customizations if needed */

.bg-gray-900 {
  /* Dark background for the modal */
}

.transition-all {
  transition: all 0.3s ease;
}
</style>
