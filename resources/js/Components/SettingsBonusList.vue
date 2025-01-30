<template>
  <div class="flex gap-6 p-6">
    <!-- Left: Settings Panel -->
    <div class="w-1/2 bg-gray-100 p-6 rounded-lg shadow-lg">
      <h2 class="text-xl font-bold mb-4">Settings Panel</h2>
      <!-- Compact Grid Layout -->
      <div class="grid grid-cols-2 gap-2">
        <!-- Border & Table Colors -->
        <div>
          <label class="block font-semibold">Table Background</label>
          <ColorPicker v-model:pureColor="settings.tableBgColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Border Color</label>
          <ColorPicker v-model:pureColor="settings.borderColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Border Width</label>
          <input type="number" v-model="settings.borderWidth" class="border p-1 w-full">
        </div>

        <div class="flex items-center">
          <input type="checkbox" v-model="settings.borderEnabled" class="mr-2">
          <label class="font-semibold">Enable Border</label>
        </div>

        <!-- Header Settings -->
        <div class="col-span-2 mt-4 border-t pt-2">
          <h3 class="text-lg font-semibold">Header</h3>
        </div>

        <div>
          <label class="block font-semibold">Background Color</label>
          <ColorPicker v-model:pureColor="settings.headerBgColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Text Color</label>
          <ColorPicker v-model:pureColor="settings.headerFontColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Font Size</label>
          <input type="number" v-model="settings.headerFontSize" class="border p-1 w-full">
        </div>

        <div class="flex items-center">
          <input type="checkbox" v-model="settings.showIcon" class="mr-2">
          <label class="font-semibold">Show Icon</label>
        </div>

        <!-- Progress Bar -->
        <div class="col-span-2 mt-4 border-t pt-2">
          <h3 class="text-lg font-semibold">Progress Bar</h3>
        </div>

        <div>
          <label class="block font-semibold">Low Color</label>
          <ColorPicker v-model:pureColor="settings.progressBarLow" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">High Color</label>
          <ColorPicker v-model:pureColor="settings.progressBarHigh" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Height (px)</label>
          <input type="number" v-model="settings.progressBarHeight" class="border p-1 w-full">
        </div>

        <!-- Subheader Settings -->
        <div class="col-span-2 mt-4 border-t pt-2">
          <h3 class="text-lg font-semibold">Subheader</h3>
        </div>

        <div>
          <label class="block font-semibold">Background Color</label>
          <ColorPicker v-model:pureColor="settings.subheaderBgColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Font Color</label>
          <ColorPicker v-model:pureColor="settings.subheaderFontColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Font Size</label>
          <input type="number" v-model="settings.subheaderFontSize" class="border p-1 w-full">
        </div>

        <!-- Table Settings -->
        <div class="col-span-2 mt-4 border-t pt-2">
          <h3 class="text-lg font-semibold">Table</h3>
        </div>

        <div>
          <label class="block font-semibold">Header BG Color</label>
          <ColorPicker v-model:pureColor="settings.tableHeaderBgColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Header Font Color</label>
          <ColorPicker v-model:pureColor="settings.tableHeaderFontColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Header Font Size</label>
          <input type="number" v-model="settings.tableHeaderFontSize" class="border p-1 w-full">
        </div>

        <div>
          <label class="block font-semibold">Body Font Color</label>
          <ColorPicker v-model:pureColor="settings.tableBodyFontColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Divider Color</label>
          <ColorPicker v-model:pureColor="settings.tableDividerColor" format="rgb" shape="square" />
        </div>

        <div>
          <label class="block font-semibold">Body Font Size</label>
          <input type="number" v-model="settings.tableBodyFontSize" class="border p-1 w-full">
        </div>

        <div>
          <label class="block font-semibold">Curent Game Color</label>
          <ColorPicker v-model:pureColor="settings.tableCurrentGameColor" format="rgb" shape="square" />
        </div>
      </div>

      <!-- Save Button -->
      <button @click="saveSettings" class="bg-blue-500 text-white px-4 py-2 rounded-md w-full mt-4">
        Save Settings
      </button>


    </div>

    <!-- Right: Preview Panel -->
    <div class="max-w-[480px]">
      <h2 class="text-xl font-bold mb-4">Preview</h2>
      <div class="p-4">
        <!-- Table -->
        <div class="table-list" :style="{
          'background-color': settings.tableBgColor,
          'border': settings.borderEnabled ? settings.borderWidth + 'px ' + settings.borderColor + ' solid' : 'unset',
        }">
          <div class="table-container">
            <div class="header-list">
              <div class="header-list-title" :style="{
                'background-color': settings.headerBgColor,
                'color': settings.headerFontColor,
                'font-size': settings.headerFontSize + 'px',
              }">
                <span class="img-list" v-if="settings.showIcon">
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="50" height="50" x="0" y="0" viewBox="0 0 365.725 365.725" xml:space="preserve" class=""><g><path d="M357.29 315.48c-.11-.111-.22-.222-.331-.331l-14.08-13.76-25.68-25.84-19.04-19.04 16.88-16.88a8 8 0 0 0 0-11.28l-18.24-18.24a8 8 0 0 0-11.28 0l-6.56 6.56-26.96-30.16 106.88-95.68a8.002 8.002 0 0 0 2.64-5.6l3.84-76.72a8 8 0 0 0-2.32-6.08 8 8 0 0 0-6.08-2.32l-76.72 3.76a8.002 8.002 0 0 0-5.6 2.64l-91.76 102.88L90.959 6.748a8.002 8.002 0 0 0-5.6-2.64L8.639.348a8 8 0 0 0-6.08 2.32 8 8 0 0 0-2.32 6.08l3.84 76.72a8.002 8.002 0 0 0 2.64 5.6l107.04 96-26.88 29.68-6.56-6.56a8 8 0 0 0-11.28 0l-18.24 18.24a8 8 0 0 0 0 11.28l16.88 16.88-19.12 18.96-40 40c-11.443 11.488-11.407 30.078.081 41.521a29.365 29.365 0 0 0 20.639 8.559 29.122 29.122 0 0 0 20.72-8.56l58.72-58.72 16.88 16.88a8 8 0 0 0 11.28 0l18.24-18.24a8 8 0 0 0 0-11.28l-6.24-6.96 34-30.32 34 30.32-6.56 6.56a8 8 0 0 0 0 11.28l18.24 18.24a8 8 0 0 0 11.28 0l16.88-16.88 19.04 19.04 25.76 25.76 13.92 13.92c11.374 11.557 29.963 11.705 41.52.331 11.557-11.373 11.705-29.963.331-41.519zm-89.851-87.452-14.08 14.08-30.08-30.08 16.56-14.8 27.6 30.8zm-248-146.96-3.2-64.56 64.56 3.2 90.88 101.6-18.56 20.72-33.6-33.6a8 8 0 0 0-11.28 11.28l34.64 34.32-18.4 20.72-105.04-93.68zm19.12 264a13.28 13.28 0 0 1-9.44 3.92c-7.358-.546-12.881-6.954-12.334-14.312a13.361 13.361 0 0 1 3.374-7.928l8-8 18.72 18.48-8.32 7.84zm19.52-19.52-18.4-18.48 14.48-14.48 18.88 18.88-14.96 14.08zm25.84-25.84-18.88-18.88 13.84-12.96 18.88 18.88-13.84 12.96zm47.2-2.16-16.8-16.8-30.16-30.16-16.48-16.56 6.88-6.88 64 64-7.44 6.4zm6-30.48-14.08-14.08 133.6-133.6a8 8 0 1 0-11.28-11.28l-133.6 133.6-14.08-14.08 32.8-36.64 154.08-171.2 64.56-3.2-3.2 64.56-110.56 98.8-98.24 87.12zm57.28-29.84 17.2-14.48 30.64 30.64-14.08 14.08-33.76-30.24zm56 43.44-16.24 17.28-6.88-6.88 64-64 6.88 6.88-16.88 16.88-30.88 29.84zm16.96 5.68 19.52-18.48 13.36 13.36-18.88 18.88-14-13.76zm39.2 39.2-14.48-14.48 18.88-18.88 14.48 14.48-18.88 18.88zm39.04 19.92-.64-.4c-5.285 5.039-13.595 5.039-18.88 0l-8-8 19.28-18.32 8 8c5.17 5.131 5.276 13.458.24 18.72z" fill="#000000" data-original="#000000"></path></g></svg>
                </span>
                <span>Bonus</span>
                <span>Hunt</span>
                <div class="bonus-details">
                  <div class="list-cost" :style="{'background-color': settings.headerCellBgColor}">
                    Cost <span>6000 {{settings.currency}}</span>
                  </div>
                  <div class="list-opened" :style="{'background-color': settings.headerCellBgColor}">
                    <div class="details"><span>12/</span></div>
                    <div class="details"><span>12</span></div>
                  </div>
                </div>
              </div>
              <div class="progress" :style="{
                'height': settings.progressBarHeight + 'px'
              }">
                <div class="progress-bar-fill" style="width: 80%;"
                :style="{
                  'background': 'linear-gradient(90deg, '+ settings.progressBarLow +' 0%, '+ settings.progressBarHigh +' 100%)'
                }"
                ></div>
              </div>
              <div class="header-details" :style="{
                'background-color': settings.subheaderBgColor,
                'color': settings.subheaderFontColor,
                'font-size': settings.subheaderFontSize + 'px'
              }">
                <div class="flex flex-col text-center">Req (x)<span>112</span></div>
                <div class="flex flex-col text-center">Avg (x)<span>143</span></div>
                <div class="flex flex-col text-center">Top (x)<span>764</span></div>
                <div class="flex flex-col text-center">Top (plata)<span>764</span></div>
                <div class="flex flex-col text-center">Rezultat<span>6947 lei</span></div>
              </div>
            </div>
            <div class="header" :style="{
              'background-color': settings.tableHeaderBgColor,
              'color': settings.tableHeaderFontColor,
              'font-size': settings.tableHeaderFontSize + 'px'
            }">
              <div class="header-content_hunt header-content">
                <div class="text-center">#</div>
                <div>Joc</div>
                <div>Miză</div>
                <div>Plată</div>
                <div>Multi</div>
              </div>
            </div>
            <div class="scroll-wrapper">
              <div class="games" :style="{
                'color': settings.tableBodyFontColor,
                'font-size': settings.tableBodyFontSize
              }">
                <div class="game-single row-game_hunt" :style="{
                  'border-bottom': '1px solid ' + settings.tableDividerColor
                }">
                  <div class="number_game">1</div>
                  <div class="name_game">RIP City</div>
                  <div class="stake">3.2</div>
                  <div>967</div>
                  <div>x302</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">2</div>
                  <div class="name_game">Dork Unit</div>
                  <div class="stake">4</div>
                  <div>185</div>
                  <div>x46</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">3</div>
                  <div class="name_game">Chaos Crew 2</div>
                  <div class="stake">4</div>
                  <div>448</div>
                  <div>x112</div>
                </div>
                <div class="game-single row-game_hunt current" :style="{'background-color': settings.tableCurrentGameColor}">
                  <div class="number_game">4</div>
                  <div class="name_game">Fist of Destruction</div>
                  <div class="stake">4</div>
                  <div>3054</div>
                  <div>x764</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">5</div>
                  <div class="name_game">2Wild2Die</div>
                  <div class="stake">4</div>
                  <div>372</div>
                  <div>x93</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">6</div>
                  <div class="name_game">Duel at Dawn</div>
                  <div class="stake">4</div>
                  <div>54</div>
                  <div>x14</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">7</div>
                  <div class="name_game">Le Viking</div>
                  <div class="stake">4</div>
                  <div>988</div>
                  <div>x247</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">8</div>
                  <div class="name_game">Book of Time</div>
                  <div class="stake">4</div>
                  <div>156</div>
                  <div>x39</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">9</div>
                  <div class="name_game">Densho</div>
                  <div class="stake">7.2</div>
                  <div>50</div>
                  <div>x7</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">10</div>
                  <div class="name_game">Rotten</div>
                  <div class="stake">4</div>
                  <div>31</div>
                  <div>x8</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">11</div>
                  <div class="name_game">Ze Zeus</div>
                  <div class="stake">8</div>
                  <div>170</div>
                  <div>x21</div>
                </div>
                <div class="game-single row-game_hunt">
                  <div class="number_game">12</div>
                  <div class="name_game">SixSixSix</div>
                  <div class="stake">8</div>
                  <div>472</div>
                  <div>x59</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
export default {
  data() {
    return {
      settings: {
        tableBgColor: "#ededed",
        borderColor: "#000",
        borderEnabled: true,
        borderWidth: 4,
        showIcon: true,
        currency: "LEI",
        headerBgColor: "#ffb700",
        headerFontSize: 16,
        headerFontColor: "#000000",
        headerCellBgColor: "rgba(255,255,255,0.85)",
        progressBarLow: "#ffb700",
        progressBarHigh: "#ff0000",
        progressBarHeight: 4,
        subheaderBgColor: "#ddd",
        subheaderFontColor: "#5a0404",
        subheaderFontSize: 14,
        tableHeaderFontSize: 16,
        tableHeaderFontColor: "#c60a0a",
        tableHeaderBgColor: "#249382",
        tableBodyFontColor: "#a55353",
        tableDividerColor: "#ff0000",
        tableBodyFontSize: 12,
        tableCurrentGameColor: "#ff0000"
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
          setting_name: "obs_bonus_list",
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
      let settingName = "obs_bonus_list";
      try {
        this.loading = true;
        const response = await axios.get(`/api/get-setting`, {
          params: { setting_name: settingName }
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


<style lang="scss">
.scroll-wrapper {
  overflow: hidden;
  position: relative;
  max-height: 100vh;
}

.games {
  display: flex;
  flex-direction: column;
}

.table-list {
  width: 100%;
  border: 4px black solid;
  border-radius: 5px;
  max-height: 100vh;
  display: block;
  overflow: hidden;
}

.header {
  position: relative;
  z-index: 1;
  background-color: #454545;
  .header-content {
    text-transform: uppercase;
    font-weight: bold;
    box-shadow: 0px -4px 20px 0px black;
  }
  .header-content_buy {
    display: grid;
    grid-template-columns: 30px 115px 50px 70px 70px 60px;
  }
  .header-content_hunt {
    display: grid;
    grid-template-columns: 30px 155px 60px 80px 70px;
  }
}

.games {
  box-sizing: border-box;
  font-size: 16px;
  overflow: inherit;
  .game-single {
    display: grid;
    border-bottom: 1px solid #d5d5d53b;
    align-content: center;
    align-items: center;
  }
  .row-game_buy {
    grid-template-columns: 30px 115px 50px 70px 70px 60px;
  }
  .row-game_hunt {
    grid-template-columns: 30px 155px 60px 80px 70px;
  }
  .number_game {
    color: #8d8d8d;
    font-size: 14px;
    padding: 5px;
  }
  .current {
    background-color: rgba(255, 166, 0, 0.329);
    .number_game {
      color: #ffc400;
    }
    .name_game:before {
      display: none;
    }
  }
}

.header-list {
  z-index: 999;
  position: relative;
  .header-list-title {
    display: flex;
    flex-direction: row;
    gap: 5px;
    align-items: center;
    text-transform: uppercase;
    font-weight: bold;
    font-size: 16px;
    padding: 10px;
    background: #ffc400;
    border-bottom: 2px black solid;
    color: black;
    .img-list {
      display: block;
      perspective: 1000px;
      margin-right: 5px;// This is essential to create the 3D effect
      svg {
        width: 20px; // Adjust width and height for better visibility
        height: 20px;
        animation: spin 10s infinite ease-in-out;
        transform-style: preserve-3d;
        position: relative;
        &::before {
          transform: translateZ(-5px); // Back side shadow
        }

        &::after {
          transform: translateZ(5px); // Front side shadow
        }
      }
    }
    .bonus-details {
      margin-left: auto;
      display: flex;
      gap: 5px;
      .list-opened {
        margin-left: auto;
        display: flex;
        background: #ffffffbf;
        padding: 0 5px;
        border-radius: 5px;
      }
      .list-cost {
        background: #ffffffbf;
        padding: 0 5px;
        border-radius: 5px;
      }
    }
  }
  .progress {
    position: relative;
    display: flex;
    justify-content: space-evenly;
    font-size: 14px;
    height: 5px;
    background-color: rgb(110 110 110);
    .details {
      z-index: 2;
      padding: 5px;
      color: #ffffff;
      text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
    }
    .progress-bar-fill {
      position: absolute;
      background: linear-gradient(90deg, rgba(255, 196, 0, 1) 0%, rgba(255, 109, 0, 1) 100%);
      border-bottom-right-radius: 10px;
      top: 0;
      bottom: 0;
      z-index: 1;
      left: 0;
    }
  }
  .progress,
  .header-details {
    span {
      margin-left: 5px;
      padding: 0 5px;
      border: 1px solid #3a3a3a;
      border-radius: 4px;
      font-weight: bold;
    }
  }
  .header-details {
    background: black;
    display: flex;
    justify-content: space-evenly;
    font-size: 16px;
    padding: 5px 0px;
  }
}
</style>