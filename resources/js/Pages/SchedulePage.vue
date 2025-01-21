<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Schedule
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="scheduler p-6">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold text-gray-700">Schedule Manager</h1>
              <button
                  @click="openCreateScheduleModal"
                  class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow hover:bg-indigo-500 focus:outline-none focus:ring focus:ring-indigo-300"
              >
                Add New Schedule
              </button>
            </div>

            <!-- Display Schedules -->
            <div class="schedule-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                  v-for="schedule in schedules"
                  :key="schedule.id"
                  class="schedule-card bg-gray-50 p-4 rounded-lg shadow-md border border-gray-200"
              >
                <h2 class="text-lg font-semibold text-gray-800">Schedule #{{ schedule.id }}</h2>
                <p class="text-sm text-gray-500">
                  Between
                  <span class="font-medium text-gray-600">{{ formatDate(schedule.days[0].date) }}</span>
                  and
                  <span class="font-medium text-gray-600">{{ formatDate(schedule.days[schedule.days.length - 1].date) }}</span>
                </p>
                <ul class="mt-4 space-y-2">
                  <li
                      v-for="day in schedule.days"
                      :key="day.id"
                      class="flex justify-between items-center bg-white p-2 rounded shadow-sm border border-gray-200"
                  >
                    <div class="text-sm text-gray-700" :class="{ 'line-through text-gray-400': !day.active }">
                      <span class="font-medium">{{ formatDayName(day.date) }}</span>: {{ day.info }}
                    </div>
                    <div class="flex space-x-2">
                      <button
                          @click="openEditDayModal(day)"
                          class="text-indigo-600 text-sm font-medium hover:underline"
                      >
                        Edit
                      </button>
                      <button
                          v-if="day.active"
                          @click="toggleDayStatus(day)"
                          class="text-red-600 text-sm font-medium hover:underline"
                      >
                        Cancel
                      </button>
                      <button
                          v-else
                          @click="toggleDayStatus(day)"
                          class="text-green-600 text-sm font-medium hover:underline"
                      >
                        Activate
                      </button>
                    </div>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Modals -->
            <CreateScheduleModal v-if="showCreateModal" @close="showCreateModal = false" />
            <EditDayModal
                v-if="showEditDayModal"
                :day="selectedDay"
                @close="showEditDayModal = false"
                @updated="fetchSchedules"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import axios from "axios";
import CreateScheduleModal from "@/Components/CreateScheduleModal.vue";
import EditDayModal from "@/Components/EditDayModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
  components: { CreateScheduleModal, EditDayModal, AppLayout },
  data() {
    return {
      schedules: [],
      showCreateModal: false,
      showEditDayModal: false,
      selectedDay: null,
    };
  },
  methods: {
    fetchSchedules() {
      axios.get("/api/schedule/all").then((response) => {
        this.schedules = response.data;
      });
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString("ro-RO");
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
      return days[new Date(date).getDay()];
    },
    openCreateScheduleModal() {
      this.showCreateModal = true;
    },
    openEditDayModal(day) {
      this.selectedDay = day;
      this.showEditDayModal = true;
    },
    toggleDayStatus(day) {
      axios.patch(`/api/schedule/${day.id}/toggle-status`).then(() => {
        day.active = !day.active;
      });
    },
  },
  mounted() {
    this.fetchSchedules();
  },
};
</script>

<style>
/* No additional custom styles as we rely on Tailwind CSS */
</style>
