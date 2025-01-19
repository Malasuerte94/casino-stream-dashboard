<template>
  <div>
    <iframe width="100%" height="600" src="https://www.youtube.com/embed/LUAl7TN0OB4?si=xzfpOlpIxILzO7TZ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
  </div>
    <div class="p-6">
        <h2 class="text-lg font-bold mb-4">Changelog - Updateuri</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="p-6 text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr class="table-row">
                        <th class="p-2 table-cell text-left">Nume</th>
                        <th class="p-2 table-cell text-left">Status</th>
                        <th class="p-2 table-cell text-left">Data</th>
                    </tr>
                </thead>
                <tbody>
                  <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                    <td class="p-2 table-cell text-left">
                      <ul>
                        <li>Bonus Battle</li>
                      </ul>
                    </td>
                    <td class="p-2 table-cell text-left">TERMINAT</td>
                    <td class="p-2 table-cell text-left">19/01/2025</td>
                  </tr>
                    <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="p-2 table-cell text-left">
                            <ul>
                                <li>ECHIPE</li>
                            </ul>
                        </td>
                        <td class="p-2 table-cell text-left">IN LUCRU</td>
                        <td class="p-2 table-cell text-left">27/08/2023</td>
                    </tr>
                    <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="p-2 table-cell text-left">
                            <ul>
                                <li>raport</li>
                                <li>retrageri</li>
                                <li>deposit</li>
                                <li>bannere</li>
                            </ul>
                        </td>
                        <td class="p-2 table-cell text-left">TERMINAT</td>
                        <td class="p-2 table-cell text-left">27/08/2023</td>
                    </tr>
                    <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="p-2 table-cell text-left">Bonus Hunt</td>
                        <td class="p-2 table-cell text-left">TERMINAT</td>
                        <td class="p-2 table-cell text-left">11/05/2023</td>
                    </tr>
                    <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="p-2 table-cell text-left">Bonus List</td>
                        <td class="p-2 table-cell text-left">TERMINAT</td>
                        <td class="p-2 table-cell text-left">11/05/2023</td>
                    </tr>
                    <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="p-2 table-cell text-left">Bonus Buy</td>
                        <td class="p-2 table-cell text-left">TERMINAT</td>
                        <td class="p-2 table-cell text-left">11/05/2023</td>
                    </tr>
                </tbody>
            </table>
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
