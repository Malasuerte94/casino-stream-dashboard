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
          <ColorPicker v-model:pureColor="settings.tableBgColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Border Color</label>
          <ColorPicker v-model:pureColor="settings.borderColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Border Width</label>
          <input type="number" v-model="settings.borderWidth" class="border p-1 w-full">
        </div>

        <div class="flex items-center col-span-2 gap-2">
          <label class="font-semibold">Currency</label>
          <input type="text" v-model="settings.currency" class="mr-2">
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
          <ColorPicker v-model:pureColor="settings.headerBgColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Text Color</label>
          <ColorPicker v-model:pureColor="settings.headerFontColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Font Size</label>
          <input type="number" v-model="settings.headerFontSize" class="border p-1 w-full">
        </div>

        <!-- Subheader Settings -->
        <div class="col-span-2 mt-4 border-t pt-2">
          <h3 class="text-lg font-semibold">Subheader</h3>
        </div>

        <div>
          <label class="block font-semibold">Background Color</label>
          <ColorPicker v-model:pureColor="settings.subheaderBgColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Font Color</label>
          <ColorPicker v-model:pureColor="settings.subheaderFontColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Background Color Round</label>
          <ColorPicker v-model:pureColor="settings.subheaderBgColorRound" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Font Color Round</label>
          <ColorPicker v-model:pureColor="settings.subheaderFontColorRound" format="rgb" shape="square"/>
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
          <label class="block font-semibold">Participant BG Color</label>
          <ColorPicker v-model:pureColor="settings.tableParticipantBgColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Participant Font Color</label>
          <ColorPicker v-model:pureColor="settings.tableParticipantFontColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Participant Font Size</label>
          <input type="number" v-model="settings.tableParticipantFontSize" class="border p-1 w-full">
        </div>

        <div>
          <label class="block font-semibold">Scores BG Color</label>
          <ColorPicker v-model:pureColor="settings.tableScoresBgColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Scores Label BG Color</label>
          <ColorPicker v-model:pureColor="settings.tableScoresLabelBgColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Scores Font Color</label>
          <ColorPicker v-model:pureColor="settings.tableScoresFontColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">Scores Font Size</label>
          <input type="number" v-model="settings.tableScoresFontSize" class="border p-1 w-full">
        </div>

        <div class="col-span-2 mt-4 border-t pt-2">
          <h3 class="text-lg font-semibold">History</h3>
        </div>

        <div class="flex items-center">
          <input type="checkbox" v-model="settings.tableHistoryEnable" class="mr-2">
          <label class="font-semibold">Show History</label>
        </div>

        <div>
          <label class="block font-semibold">History Bg Colors</label>
          <ColorPicker v-model:pureColor="settings.tableHistoryBgOddColor" format="rgb" shape="square"/>
          <ColorPicker v-model:pureColor="settings.tableHistoryBgEvenColor" format="rgb" shape="square"/>
        </div>

        <div>
          <label class="block font-semibold">History Font Size</label>
          <input type="number" v-model="settings.tableBodyFontSize" class="border p-1 w-full">
        </div>
      </div>

      <!-- Save Button -->
      <button @click="saveSettings" class="bg-blue-500 text-white px-4 py-2 rounded-md w-full mt-4">
        Save Settings
      </button>


    </div>

    <!-- Right: Preview Panel -->
    <div class="max-w-[480px] preview">
      <h2 class="text-xl font-bold mb-4">Preview</h2>
      <div class="p-4">

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
                <span>Bonus Battle #9</span>
                <div class="bonus-details">
                  <div class="list-cost">Miză <span>5-10 lei</span></div>
                  <div class="list-opened">
                    <div class="details"><span>4 Jocuri</span></div>
                  </div>
                </div>
              </div>
              <div class="header-details" :style="{
                'background-color': settings.subheaderBgColor,
                'color': settings.subheaderFontColor,
                'font-size': settings.subheaderFontSize + 'px',
              }">
                <div class="text-center uppercase stage font-bold flex justify-center items-center"
                     :style="{
                  'background-color': settings.subheaderBgColorRound,
                  'color': settings.subheaderFontColorRound,
                }"
                >Final
                </div>
                <div><span>AVG: 1.53</span></div>
                <div><span>TOP: 8.05</span></div>
                <div><span class="text-green-500">4464 lei </span></div>
              </div>
            </div>
            <div class="main-battle">
              <div class="battle flex gap-2 align-middle items-center justify-center relative">
                <div class="w-100 grow flex-col flex gap-2 px-2 max-w-[190px]">
                  <div class="flex flex-col gap-2 mt-2">
                    <div class="concurrent flex rounded-md text-white"><img
                        src="/storage/games/hacksaw-hand-of-anubis.jpg" alt="Game Thumbnail"
                        class="w-[100px] rounded-lg"></div>
                    <div class="from-user"
                         :style="{
                  'background-color': settings.tableParticipantBgColor,
                  'color': settings.tableParticipantFontColor,
                  'font-size': settings.tableParticipantFontSize + 'px',
                }">dfshsdfh
                    </div>
                  </div>
                  <table
                      class="table-auto mb-2 w-full border-collapse rounded-md overflow-hidden"
                      :style="{
                  'color': settings.tableScoresFontColor,
                  'font-size': settings.tableScoresFontSize + 'px',
                }">
                    <thead>
                    <tr class="bg-black text-white uppercase" :style="{
                  'background-color': settings.tableScoresLabelBgColor
                }">
                      <th class=" px-1 py-1">Cost</th>
                      <th class=" px-1 py-1">Rezultat</th>
                      <th class=" px-1 py-1">Scor</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr :style="{
                  'background-color': settings.tableScoresBgColor
                }">
                      <td class=" px-2 py-1">0</td>
                      <td class=" px-2 py-1">0</td>
                      <td class=" px-2 py-1 font-bold text-red-500">0.00</td>
                    </tr>
                    <tr :style="{
                  'background-color': settings.tableScoresBgColor
                }">
                      <td class="px-2 py-1">0</td>
                      <td class="px-2 py-1">0</td>
                      <td class="px-2 py-1 font-bold text-red-500">0.00</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <div class="w-100 grow flex-col flex gap-2 px-2 max-w-[190px]">
                  <div class="flex flex-col gap-2 mt-2">
                    <div class="concurrent flex rounded-md text-white"><img src="/storage/games/hacksaw-evil-eyes.jpg"
                                                                            alt="Game Thumbnail"
                                                                            class="w-[100px] rounded-lg"></div>
                    <div class="from-user" :style="{
                  'background-color': settings.tableParticipantBgColor,
                  'color': settings.tableParticipantFontColor,
                  'font-size': settings.tableParticipantFontSize + 'px',
                }">asdgaghsd
                    </div>
                  </div>
                  <table
                      class="table-auto mb-2 w-full border-collapse rounded-md overflow-hidden"
                      :style="{
                  'color': settings.tableScoresFontColor,
                  'font-size': settings.tableScoresFontSize + 'px',
                }"
                  >
                    <thead>
                    <tr class="uppercase" :style="{
                  'background-color': settings.tableScoresLabelBgColor,
                }">
                      <th class=" px-1 py-1">Cost</th>
                      <th class=" px-1 py-1">Rezultat</th>
                      <th class=" px-1 py-1">Scor</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr :style="{
                  'background-color': settings.tableScoresBgColor
                }">
                      <td class=" px-2 py-1">0</td>
                      <td class=" px-2 py-1">0</td>
                      <td class=" px-2 py-1 font-bold text-red-500">0.00</td>
                    </tr>
                    <tr :style="{
                  'background-color': settings.tableScoresBgColor
                }">
                      <td class=" px-2 py-1">0</td>
                      <td class=" px-2 py-1">0</td>
                      <td class=" px-2 py-1 font-bold text-red-500">0.00</td>
                    </tr>
                    </tbody>
                  </table>
                </div><!-- VS Symbol -->
                <div class="vs-symbol flex justify-center items-center text-2xl"><img
                    src="/storage/assets/images/vs.gif" alt="vs"></div>
              </div>
            </div>
            <div class="second-battle" v-if="settings.tableHistoryEnable">
              <div class="shadow-md py-2 px-2">
                <div class="overflow-x-auto">
                  <table class="table-auto w-full text-gray-200" :style="{
                  'font-size': settings.tableBodyFontSize + 'px'
                }">
                    <tbody>
                    <tr :style="{
                  'background-color': settings.tableHistoryBgOddColor
                }">
                      <td class="border border-black px-1 py-1 shrink w-0">❌</td>
                      <td class="border border-black px-2 py-1">Tasty Treats</td>
                      <td class="border border-black px-2 py-1">dgfjghdf</td>
                    </tr>
                    <tr :style="{
                  'background-color': settings.tableHistoryBgEvenColor
                }">
                      <td class="border border-black px-1 py-1 shrink w-0">❌</td>
                      <td class="border border-black px-2 py-1">Cash Compass</td>
                      <td class="border border-black px-2 py-1">asdfasdf</td>
                    </tr>
                    <tr :style="{
                  'background-color': settings.tableHistoryBgOddColor
                }">
                      <td class="border border-black px-1 py-1 shrink w-0">✅</td>
                      <td class="border border-black px-2 py-1">Hand of Anubis</td>
                      <td class="border border-black px-2 py-1">dfshsdfh</td>
                    </tr>
                    <tr :style="{
                  'background-color': settings.tableHistoryBgEvenColor
                }">
                      <td class="border border-black px-1 py-1 shrink w-0">✅</td>
                      <td class="border border-black px-2 py-1">Evil Eyes</td>
                      <td class="border border-black px-2 py-1">asdgaghsd</td>
                    </tr>
                    </tbody>
                  </table>
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
import {ColorPicker} from "vue3-colorpicker";
import "vue3-colorpicker/style.css";

export default {
  data() {
    return {
      settings: {
        tableBgColor: "#ededed",
        borderColor: "#000",
        borderEnabled: true,
        borderWidth: 4,
        currency: "LEI",
        headerBgColor: "#ffb700",
        headerFontSize: 16,
        headerFontColor: "#000000",
        subheaderBgColorRound: "rgba(255,255,255,0.85)",
        subheaderFontColorRound: "#ffb700",
        tableParticipantFontColor: "#ff0000",
        tableParticipantBgColor: "#ff0000",
        subheaderBgColor: "#ddd",
        subheaderFontColor: "#5a0404",
        subheaderFontSize: 14,
        tableParticipantFontSize: 16,
        tableScoresBgColor: "#c60a0a",
        tableScoresLabelBgColor: "#249382",
        tableScoresFontColor: "#a55353",
        tableHistoryBgOddColor: "#ff0000",
        tableBodyFontSize: 12,
        tableHistoryBgEvenColor: "#ff0000",
        tableScoresFontSize: 12,
        tableHistoryEnable: true,
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
          setting_name: "obs_bonus_battle",
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
      let settingName = "obs_bonus_battle";
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


<style lang="scss">
.preview {
  background-image: url("https://t3.ftcdn.net/jpg/03/23/92/96/360_F_323929694_BKkGKE8dWIWqpG91tzYFC3GHPNdjg6mh.jpg");
}
.bracket-container {
  display: flex;
  flex-direction: column;
  gap: 5px;
  border-radius: 5px;
  color: #ffffff;
  font-family: Arial, sans-serif;

  .bracket-item {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    padding: 5px;
    background-color: black;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  .participant {
    font-size: 14px;
    font-weight: bold;
    transition: opacity 0.3s, text-decoration 0.3s;
  }

  .participant.loser {
    text-decoration: line-through;
    opacity: 0.5;
  }

  .vs {
    font-size: 10px;
    font-weight: bold;
    color: #ff5722;
  }
}

.table-list {
  width: 100%;
  border: 4px black solid;
  border-radius: 5px;
  max-height: 100vh;
  display: block;
  overflow: hidden;
}

.vs-symbol {
  position: absolute;
  width: 100px;
  height: 100px;
  z-index: 2;
  align-items: center;
  align-self: baseline;
  top: 24px;
}

.concurrent {
  height: auto;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  z-index: 1;
}

.from-user {
  text-align: center;
  font-size: 14px;
  background-color: black;
  border-radius: 5px;
}

.stage {
  background-color: #FFC400;
  padding: 0 10px;
  border-radius: 5px;
  color: black;
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
    padding: 10px;
    border-bottom: 2px black solid;

    .img-list {
      display: block;
      perspective: 1000px;
      margin-right: 5px; // This is essential to create the 3D effect
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
        background: rgb(255 255 255 / 27%);
        padding: 0 5px;
        border-radius: 5px;
      }

      .list-cost {
        background: rgb(255 255 255 / 27%);
        padding: 0 5px;
        border-radius: 5px;
      }
    }
  }

  .header-details {
    background: black;
    display: flex;
    justify-content: space-between;
    padding: 5px 5px;

    span {
      margin-left: 5px;
      padding: 0 5px;
      border: 1px solid #3a3a3a;
      border-radius: 4px;
      font-weight: bold;
    }
  }
}
</style>