<template>
  <div class="flex flex-row w-full">
    <div class="w-1/2 p-4">
      <iframe
          class="w-full h-[600px]"
          src="https://www.youtube.com/embed/LUAl7TN0OB4?si=xzfpOlpIxILzO7TZ"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen>
      </iframe>
    </div>
    <div class="w-1/2 p-6">
      <h2 class="text-lg font-bold mb-4">Changelog - Updateuri</h2>
      <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
          <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th class="p-3">Nume</th>
            <th class="p-3">Status</th>
            <th class="p-3">Data</th>
          </tr>
          </thead>
          <tbody>
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td class="p-3">Bonus Battle</td>
            <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
            <td class="p-3">19/01/2025</td>
          </tr>
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td class="p-3">ECHIPE</td>
            <td class="p-3 text-yellow-500 font-semibold">IN LUCRU</td>
            <td class="p-3">27/08/2023</td>
          </tr>
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td class="p-3">
              <ul>
                <li>raport</li>
                <li>retrageri</li>
                <li>deposit</li>
                <li>bannere</li>
              </ul>
            </td>
            <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
            <td class="p-3">27/08/2023</td>
          </tr>
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td class="p-3">Bonus Hunt</td>
            <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
            <td class="p-3">11/05/2023</td>
          </tr>
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td class="p-3">Bonus List</td>
            <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
            <td class="p-3">11/05/2023</td>
          </tr>
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td class="p-3">Bonus Buy</td>
            <td class="p-3 text-green-500 font-semibold">TERMINAT</td>
            <td class="p-3">11/05/2023</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script >
export default {
    props: {
        stream: Object,
    },
    data() {
    },
};
</script>
