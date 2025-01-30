<template>
  <div class="flex gap-6 p-6">
    <!-- Left: Settings Panel -->
    <div class="w-1/2 bg-gray-100 p-6 rounded-lg shadow-lg">
      <h2 class="text-xl font-bold mb-4">Settings Panel</h2>
      <!-- Compact Grid Layout -->
      <div class="grid grid-cols-2 gap-2">
        <div>
          <label class="block font-semibold">Cell Background</label>
          <ColorPicker v-model:pureColor="settings.cellBg" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Cell Background Short</label>
          <ColorPicker v-model:pureColor="settings.cellBgShort" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Cell Active Background</label>
          <ColorPicker v-model:pureColor="settings.cellBgActive" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Font Color</label>
          <ColorPicker v-model:pureColor="settings.cellFontColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Title Font Size</label>
          <input type="number" v-model="settings.cellTitleFontSize" class="border p-1 w-full">
        </div>

        <div>
          <label class="block font-semibold">Subtitle Font Size</label>
          <input type="number" v-model="settings.cellSubtitleFontSize" class="border p-1 w-full">
        </div>
      </div>

      <!-- Save Button -->
      <button @click="saveSettings" class="bg-blue-500 text-white px-4 py-2 rounded-md w-full mt-4">
        Save Settings
      </button>


    </div>

    <!-- Right: Preview Panel -->
    <div class="w-1/2 bg-gray-100 p-6 rounded-lg shadow-lg">
      <div data-v-45ae16f1="" class="schedule-card bg-gray-900 p-4 rounded-lg shadow-md border border-gray-700"
           :style="{'background-color': settings.cellBg, 'color': settings.cellFontColor}"><h3
          data-v-45ae16f1="" class="text-md font-semibold text-gray-200">Program</h3>
        <ul data-v-45ae16f1="" class="mt-4 space-y-2">
          <li data-v-45ae16f1=""
              class="flex justify-between items-center p-2 rounded shadow-sm border border-gray-600 opacity-50 line-through text-white bg-gray-800"
              :style="{'background-color': settings.cellBgShort}">
            <span data-v-45ae16f1="" class="text-lg font-bold" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Luni</span><span data-v-45ae16f1=""
                                                                                      class="text-lg" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">tes</span></li>
          <li data-v-45ae16f1=""
              class="flex justify-between items-center p-2 rounded shadow-sm border border-gray-600 bg-gray-800"
              :style="{'background-color': settings.cellBgShort}"><span
              data-v-45ae16f1="" class="text-lg font-bold" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Marți</span><span data-v-45ae16f1=""
                                                                             class="text-lg" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">test</span></li>
          <li data-v-45ae16f1=""
              class="flex justify-between items-center p-2 rounded shadow-sm border border-gray-600 bg-gray-800"
              :style="{'background-color': settings.cellBgShort}"><span
              data-v-45ae16f1="" class="text-lg font-bold" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Miercuri</span><span data-v-45ae16f1=""
                                                                                class="text-lg" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">asd</span></li>
          <li data-v-45ae16f1=""
              class="flex justify-between items-center p-2 rounded shadow-sm border border-gray-600 bg-[#FFC400] text-gray-900"
              :style="{'background-color': settings.cellBgActive}">
            <span data-v-45ae16f1="" class="text-lg font-bold" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Joi</span><span data-v-45ae16f1=""
                                                                               class="text-lg" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">sadg</span></li>
          <li data-v-45ae16f1=""
              class="flex justify-between items-center p-2 rounded shadow-sm border border-gray-600 bg-gray-800"
              :style="{'background-color': settings.cellBgShort}"><span
              data-v-45ae16f1="" class="text-lg font-bold" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Vineri</span><span data-v-45ae16f1=""
                                                                              class="text-lg" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">asdf</span></li>
        </ul>
      </div>


      <table data-v-45ae16f1="" class="w-full text-sm text-gray-200 border-collapse">
        <tbody data-v-45ae16f1="">
        <tr data-v-45ae16f1="">
          <td data-v-45ae16f1="" class="p-1 border border-transparent"><!-- Day Card -->
            <div data-v-45ae16f1="" class="bg-gray-800 opacity-75 line-through p-2 rounded-lg shadow-md text-left"
                 :style="{'background-color': settings.cellBgShort, 'color': settings.cellFontColor}">
              <div data-v-45ae16f1="" class="font-bold text-lg mb-1" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Luni</div>
              <div data-v-45ae16f1="" class="text-sm" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">tes</div>
            </div>
          </td>
          <td data-v-45ae16f1="" class="p-1 border border-transparent"><!-- Day Card -->
            <div data-v-45ae16f1="" class="bg-gray-900 p-2 rounded-lg shadow-md text-left"
                 :style="{'background-color': settings.cellBgShort , 'color': settings.cellFontColor}">
              <div data-v-45ae16f1="" class="font-bold text-lg mb-1" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Marți</div>
              <div data-v-45ae16f1="" class="text-sm" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">test</div>
            </div>
          </td>
          <td data-v-45ae16f1="" class="p-1 border border-transparent"><!-- Day Card -->
            <div data-v-45ae16f1="" class="bg-gray-900 p-2 rounded-lg shadow-md text-left"
                 :style="{'background-color': settings.cellBgShort , 'color': settings.cellFontColor}">
              <div data-v-45ae16f1="" class="font-bold text-lg mb-1" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Miercuri</div>
              <div data-v-45ae16f1="" class="text-sm" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">asd</div>
            </div>
          </td>
          <td data-v-45ae16f1="" class="p-1 border border-transparent"><!-- Day Card -->
            <div data-v-45ae16f1="" class="bg-[#FFC400] text-gray-900 p-2 rounded-lg shadow-md text-left"
                 :style="{'background-color': settings.cellBgActive , 'color': settings.cellFontColor}">
              <div data-v-45ae16f1="" class="font-bold text-lg mb-1" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Joi</div>
              <div data-v-45ae16f1="" class="text-sm" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">sadg</div>
            </div>
          </td>
        </tr>
        <tr data-v-45ae16f1="">
          <td data-v-45ae16f1="" class="p-1 border border-transparent"><!-- Day Card -->
            <div data-v-45ae16f1="" class="bg-gray-900 p-2 rounded-lg shadow-md text-left"
                 :style="{'background-color': settings.cellBgShort , 'color': settings.cellFontColor}">
              <div data-v-45ae16f1="" class="font-bold text-lg mb-1" :style="{'font-size': settings.cellTitleFontSize + 'px'}">Vineri</div>
              <div data-v-45ae16f1="" class="text-sm" :style="{'font-size': settings.cellSubtitleFontSize + 'px'}">asdf</div>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import {ColorPicker} from "vue3-colorpicker";
import "vue3-colorpicker/style.css";

export default {
  data() {
    return {
      settings: {
        cellBg: "#3a3a3a",
        cellBgShort: "#252525",
        cellBgActive: "#ff5c00",
        cellFontColor: "#ffffff",
        cellTitleFontSize: 14,
        cellSubtitleFontSize: 14,
      }
    };
  },
  components: {
    ColorPicker,
  },
  methods: {
    async saveSettings() {
      try {
        this.loading = true;
        await axios.post("/api/settings/save", {
          setting_name: "obs_schedule",
          setting_value: JSON.stringify(this.settings),
        });
      } catch (error) {
        console.error("Error saving settings:", error);
        this.error = "Failed to save settings.";
      } finally {
        await this.loadSettings()
        this.loading = false;
      }
    },
    async loadSettings() {
      let settingName = "obs_schedule";
      try {
        this.loading = true;
        const response = await axios.get(`/api/get-setting`, {
          params: {setting_name: settingName}
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
    }
  },
  mounted() {
    this.loadSettings();
  },
};
</script>