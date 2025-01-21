<template>
  <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
    <div class="modal bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Day</h2>
      <form @submit.prevent="submitEdit">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Date</label>
          <input
              type="text"
              v-model="day.date"
              class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              disabled
          />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Info</label>
          <input
              type="text"
              v-model="day.info"
              class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>
        <div class="flex justify-end space-x-4">
          <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md shadow hover:bg-gray-200"
          >
            Cancel
          </button>
          <button
              type="submit"
              class="px-4 py-2 text-white bg-indigo-600 rounded-md shadow hover:bg-indigo-500 focus:outline-none focus:ring focus:ring-indigo-300"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    day: {
      type: Object,
      required: true,
    },
  },
  methods: {
    submitEdit() {
      axios.patch(`/api/schedule/${this.day.id}/edit`, { info: this.day.info }).then(() => {
        this.$emit("updated");
        this.$emit("close");
      });
    },
  },
};
</script>
