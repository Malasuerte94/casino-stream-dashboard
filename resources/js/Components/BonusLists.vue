<template>
  <div>
    <!-- Top Link & Controls -->
    <div class="px-6 py-3 flex items-center justify-between bg-gray-800 rounded-md shadow-md transition-all duration-300">
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-300">LINK pentru View-eri (ghicește suma)</span>
        <div class="text-sm font-bold py-1 px-3 bg-gray-700 rounded-md border border-gray-600 transition">
          {{ guessUrl }}
        </div>
      </div>
      <div class="flex items-center space-x-4">
        <!-- Toggle Switch -->
        <label class="inline-flex items-center cursor-pointer">
          <span class="text-sm font-medium text-gray-300 mr-2">
            Înscrieri {{ open_list ? 'Pornite' : 'Oprite' }}
          </span>
          <input
              type="checkbox"
              true-value="true"
              false-value="false"
              v-model="open_list"
              @change="setStatusGuessList"
              class="sr-only peer"
          />
          <div class="w-11 h-6 bg-gray-700 border border-gray-600 rounded-full peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-800 transition">
            <div class="w-5 h-5 bg-white rounded-full transform transition-transform duration-300 peer-checked:translate-x-5"></div>
          </div>
        </label>
        <!-- Action Button -->
        <button
            :disabled="isEnded"
            :class="{'opacity-50 cursor-not-allowed': isEnded}"
            @click="activateDialog"
            type="button"
            class="bg-red-600 hover:bg-red-700 text-white font-medium rounded-md text-sm px-5 py-2 transition-all duration-300"
        >
          ÎNCHIDE LISTA / DECLARĂ CÂȘTIGĂTOR
        </button>
      </div>
    </div>

    <!-- Bonus List Settings & Content -->
    <div class="bg-gray-800 rounded-md shadow-md mt-4 transition-all duration-300">
      <div class="p-6">
        <div class="grid grid-cols-2 gap-4">
          <!-- Radio for Bonus Buy -->
          <div
              class="flex items-center pl-4 p-2 rounded-md bg-gray-700 border border-gray-600 cursor-pointer transition-all duration-300"
              :class="{'bg-indigo-600': settings.bonus_list === 'buy'}"
              @click="settings.bonus_list = 'buy'; updateSettings()"
          >
            <input
                @change="updateSettings"
                id="bordered-radio-2"
                type="radio"
                value="buy"
                v-model="settings.bonus_list"
                name="bonus-list-display"
                class="w-4 h-4 mr-2"
            />
            <label for="bordered-radio-2" class="w-full py-1 text-gray-300">
              Afișează Buy
            </label>
          </div>
          <!-- Radio for Bonus Hunt -->
          <div
              class="flex items-center pl-4 p-2 rounded-md bg-gray-700 border border-gray-600 cursor-pointer transition-all duration-300"
              :class="{'bg-indigo-600': settings.bonus_list === 'hunt'}"
              @click="settings.bonus_list = 'hunt'; updateSettings()"
          >
            <input
                @change="updateSettings"
                id="bordered-radio-1"
                type="radio"
                value="hunt"
                v-model="settings.bonus_list"
                name="bonus-list-display"
                class="w-4 h-4 mr-2"
            />
            <label for="bordered-radio-1" class="w-full py-1 text-gray-300">
              Afișează Hunt
            </label>
          </div>
        </div>
      </div>
      <!-- Bonus Buy List -->
      <transition name="fade">
        <div v-if="settings.bonus_list == 'buy'" class="p-6 bg-gray-800 rounded-md border border-gray-700 mt-4 transition-all duration-300" :key="componentKey">
          <h5 class="text-lg font-bold text-gray-200 mb-2">Bonus Buy - List</h5>
          <BonusBuy @newlist="getUrlGuessList()" />
        </div>
      </transition>
      <!-- Bonus Hunt List -->
      <transition name="fade">
        <div v-if="settings.bonus_list == 'hunt'" class="p-6 bg-gray-800 rounded-md border border-gray-700 mt-4 transition-all duration-300" :key="componentKey">
          <h5 class="text-lg font-bold text-gray-200 mb-2">Bonus Hunt - List</h5>
          <BonusHunt @newlist="getUrlGuessList()" />
        </div>
      </transition>
    </div>
  </div>
</template>
<script>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import BonusBuy from "@/Components/MainComponents/BonusBuy.vue";
import BonusHunt from "@/Components/MainComponents/BonusHunt.vue";
import StreamBoard from "@/Components/MainComponents/StreamBoard.vue";
import { initFlowbite } from "flowbite";
import axios from "axios";

export default {
  components: { ApplicationLogo, BonusBuy, BonusHunt, StreamBoard },
  data() {
    return {
      open_list: false,
      settings: [],
      latestList: "",
      listCost: 0,
      componentKey: 0,
      listEnded: 0,
    };
  },
  async mounted() {
    initFlowbite();
    await this.getSettings();
    await this.getUrlGuessList();
  },
  methods: {
    async getSettings() {
      await axios
          .get("/api/settings")
          .then((response) => {
            this.settings = response.data.settings;
          })
          .catch((error) => {
            console.log(error);
          });
      if (this.settings.bonus_list) {
        this.$emit("displayOnly", this.settings.bonus_list);
      }
    },
    async updateSettings() {
      await axios
          .patch("/api/settings", {
            settings: this.settings,
          })
          .then((response) => {
            this.settings = response.data.settings;
          })
          .catch((error) => {
            console.log(error);
          });
      await this.getSettings();
    },
    async getUrlGuessList() {
      await axios
          .get("/api/get-latest-list")
          .then((response) => {
            this.listEnded = response.data.bonusList.ended;
            this.latestList = response.data["list-id"];
            this.open_list = response.data["is_open"] === 1;
            this.listCost = response.data.bonusList.start;
          });
    },
    async setStatusGuessList() {
      await axios
          .post("/api/set-latest-list", {
            is_open: this.open_list,
            list_id: this.latestList,
            type: this.settings.bonus_list,
          })
          .then(() => {
            this.getUrlGuessList();
          });
    },
    async closeBonusList() {
      await axios
          .post("/api/close-bonus-list", {
            close: true,
            list_id: this.latestList,
            type: this.settings.bonus_list,
          })
          .finally(() => {
            this.componentKey++;
          });
    },
    activateDialog() {
      if (this.listCost <= 0) {
        alert("Vă rugăm să introduceți costul listei (Costul Hunt-ului).");
        return;
      }
      this.$dialog({
        message:
            "Ești sigur că vrei să închizi lista? Nu mai poți edita după închidere. Câștigătorii vor fi anunțați!",
        buttons: ["da", "nu"],
        da: () => {
          this.closeBonusList();
        },
        nu: () => {
          console.log("refuse");
        },
      });
    },
  },
  computed: {
    guessUrl() {
      const host = window.location.origin;
      return `${host}/guess-list/${this.latestList}/${this.settings.bonus_list}`;
    },
    isEnded() {
      return Boolean(this.listEnded);
    },
  },
  watch: {
    settings() {
      this.getUrlGuessList();
    },
  },
};
</script>
