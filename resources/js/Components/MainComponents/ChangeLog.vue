<template>
  <div class="flex flex-col sm:flex-row w-full gap-4">
    <!-- Left Column: Tutorials -->
    <div class="sm:w-1/2 p-4 bg-gray-800 rounded-lg border border-gray-700 shadow-lg text-gray-200">
      Tutorials Soon
    </div>

    <!-- Right Column: Changelog Table -->
    <div class="sm:w-1/2 p-6">
      <h2 class="text-lg font-bold mb-4 text-gray-200">Changelog - Updateuri</h2>
      <transition name="fade">
        <div class="relative overflow-x-auto shadow-lg sm:rounded-lg mt-6">
          <table class="w-full text-sm text-left text-gray-300">
            <thead class="text-xs uppercase bg-gray-700 text-gray-400">
            <tr>
              <th class="p-3">Nume</th>
              <th class="p-3">Status</th>
              <th class="p-3">Data</th>
            </tr>
            </thead>
            <tbody>
            <tr class="bg-gray-800 border-b border-gray-700">
              <td class="p-3">Bonus Battle</td>
              <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
              <td class="p-3">19/01/2025</td>
            </tr>
            <tr class="bg-gray-800 border-b border-gray-700">
              <td class="p-3">ECHIPE</td>
              <td class="p-3 text-yellow-500 font-semibold">IN LUCRU</td>
              <td class="p-3">27/08/2023</td>
            </tr>
            <tr class="bg-gray-800 border-b border-gray-700">
              <td class="p-3">
                <ul class="list-disc pl-5">
                  <li>raport</li>
                  <li>retrageri</li>
                  <li>deposit</li>
                  <li>bannere</li>
                </ul>
              </td>
              <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
              <td class="p-3">27/08/2023</td>
            </tr>
            <tr class="bg-gray-800 border-b border-gray-700">
              <td class="p-3">Bonus Hunt</td>
              <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
              <td class="p-3">11/05/2023</td>
            </tr>
            <tr class="bg-gray-800 border-b border-gray-700">
              <td class="p-3">Bonus List</td>
              <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
              <td class="p-3">11/05/2023</td>
            </tr>
            <tr class="bg-gray-800 border-b border-gray-700">
              <td class="p-3">Bonus Buy</td>
              <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
              <td class="p-3">11/05/2023</td>
            </tr>
            </tbody>
          </table>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stream: Object
  }
}
</script>

<style scoped>
/* Fade Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
