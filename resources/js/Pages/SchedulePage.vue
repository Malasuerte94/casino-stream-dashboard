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
                  class="schedule-card bg-gray-50 p-4 rounded-lg shadow-md border border-gray-200 relative"
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
                <!-- Discord Button -->
                <button
                    @click="openDiscordModal(schedule.id)"
                    class="absolute h-[20px] w-[20px] top-2 right-2 flex items-center justify-center bg-purple-600 text-white rounded-full shadow-md hover:bg-purple-500 focus:outline-none focus:ring focus:ring-purple-300"
                    title="Announce to Discord"
                >
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      viewBox="0 0 24 24"
                      width="12"
                      height="12"
                      fill="#FFFFFF"
                  ><path d="M19.54 0c1.356 0 2.46 1.104 2.46 2.472v21.528l-2.58-2.28-1.452-1.344-1.536-1.428.636 2.22h-13.608c-1.356 0-2.46-1.104-2.46-2.472v-16.224c0-1.368 1.104-2.472 2.46-2.472h16.08zm-4.632 15.672c2.652-.084 3.672-1.824 3.672-1.824 0-3.864-1.728-6.996-1.728-6.996-1.728-1.296-3.372-1.26-3.372-1.26l-.168.192c2.04.624 2.988 1.524 2.988 1.524-1.248-.684-2.472-1.02-3.612-1.152-.864-.096-1.692-.072-2.424.024l-.204.024c-.42.036-1.44.192-2.724.756-.444.204-.708.348-.708.348s.996-.948 3.156-1.572l-.12-.144s-1.644-.036-3.372 1.26c0 0-1.728 3.132-1.728 6.996 0 0 1.008 1.74 3.66 1.824 0 0 .444-.54.804-.996-1.524-.456-2.1-1.416-2.1-1.416l.336.204.048.036.047.027.014.006.047.027c.3.168.6.3.876.408.492.192 1.08.384 1.764.516.9.168 1.956.228 3.108.012.564-.096 1.14-.264 1.74-.516.42-.156.888-.384 1.38-.708 0 0-.6.984-2.172 1.428.36.456.792.972.792.972zm-5.58-5.604c-.684 0-1.224.6-1.224 1.332 0 .732.552 1.332 1.224 1.332.684 0 1.224-.6 1.224-1.332.012-.732-.54-1.332-1.224-1.332zm4.38 0c-.684 0-1.224.6-1.224 1.332 0 .732.552 1.332 1.224 1.332.684 0 1.224-.6 1.224-1.332 0-.732-.54-1.332-1.224-1.332z"/></svg>
                </button>
              </div>
            </div>

            <!-- Modals -->
            <CreateScheduleModal v-if="showCreateModal" @close="closeAndRefresh()" />
            <EditDayModal
                v-if="showEditDayModal"
                :day="selectedDay"
                @close="showEditDayModal = false"
                @updated="fetchSchedules"
            />
            <div
                v-if="showDiscordModal"
                class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50"
            >
              <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-gray-700 mb-4">Announce to Discord</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to announce this schedule to Discord?</p>
                <div class="flex gap-4 justify-end">
                  <button
                      @click="announceToDiscord()"
                      class="px-4 py-2 bg-purple-600 text-white rounded-md shadow hover:bg-purple-500 focus:outline-none focus:ring focus:ring-purple-300"
                  >
                    Yes
                  </button>
                  <button
                      @click="closeDiscordModal"
                      class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md shadow hover:bg-gray-200"
                  >
                    No
                  </button>
                </div>
              </div>
            </div>
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
      showDiscordModal: false,
      selectedDay: null,
      selectedScheduleId: null, // Store the schedule ID for Discord
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
      const days = ["Duminică", "Luni", "Marți", "Miercuri", "Joi", "Vineri", "Sâmbătă"];
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
    openDiscordModal(scheduleId) {
      this.selectedScheduleId = scheduleId;
      this.showDiscordModal = true;
    },
    closeDiscordModal() {
      this.selectedScheduleId = null;
      this.showDiscordModal = false;
    },
    announceToDiscord() {
      axios.post(`/api/schedule/${this.selectedScheduleId}/announce`).then(() => {
        this.closeDiscordModal();
        alert("Schedule announced to Discord successfully!");
      });
    },
    closeAndRefresh() {
      this.showCreateModal = false;
      this.fetchSchedules();
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
