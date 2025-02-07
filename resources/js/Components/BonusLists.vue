<template>
  <div>
    <!-- Top Link & Controls -->
    <div class="px-6 py-2 flex w-full flex-row items-center justify-between bg-gray-800 rounded-lg shadow">
      <div class="flex items-center">
        <span class="mr-4 text-gray-300">LINK pentru View-eri (ghicește suma)</span>
        <div class="text-sm font-bold py-2 px-4 bg-gray-700 rounded text-white border border-gray-600">
          {{ guessUrl }}
        </div>
      </div>
      <div class="flex gap-2">
        <!-- Toggle Button for Open/Close List -->
        <label class="inline-flex cursor-pointer p-2 w-full items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
          <span class="uppercase mr-2">Înscrieri {{ open_list ? 'Pornite' : 'Oprite' }}</span>
          <input
              type="checkbox"
              true-value="true"
              false-value="false"
              v-model="open_list"
              @change="setStatusGuessList"
              class="sr-only peer"
          />
          <div class="relative w-11 h-6 bg-gray-700 border border-gray-600 rounded-full peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800 transition">
            <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
          </div>
        </label>
        <!-- Close List / Declare Winner Button -->
        <button
            :disabled="isEnded"
            :class="{'opacity-50 cursor-not-allowed': isEnded}"
            @click="activateDialog"
            type="button"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition"
        >
          ÎNCHIDE LISTA / DECLARĂ CÂȘTIGĂTOR
        </button>
      </div>
    </div>

    <!-- Bonus List Settings & Content -->
    <div class="bg-gray-800 bg-opacity-25 grid grid-cols-1 mt-4 rounded-lg">
      <div class="p-6">
        <div class="grid grid-cols-2 gap-4">
          <!-- Radio for Bonus Buy -->
          <div
              class="flex items-center pl-4 p-2 rounded-lg bg-gray-700 border border-gray-600 cursor-pointer transition"
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
            <label for="bordered-radio-2" class="w-full py-2 ml-2 text-gray-300">
              Afișează Buy
            </label>
          </div>
          <!-- Radio for Bonus Hunt -->
          <div
              class="flex items-center pl-4 p-2 rounded-lg bg-gray-700 border border-gray-600 cursor-pointer transition"
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
            <label for="bordered-radio-1" class="w-full py-2 ml-2 text-gray-300">
              Afișează Hunt
            </label>
          </div>
        </div>
      </div>
      <!-- Bonus Buy List -->
      <transition name="fade">
        <div v-if="settings.bonus_list == 'buy'" class="p-6 bg-gray-800 rounded-lg border border-gray-700 mt-4" :key="componentKey">
          <h5 class="text-lg font-bold text-gray-200">Bonus Buy - List</h5>
          <BonusBuy @newlist="getUrlGuessList()" />
        </div>
      </transition>
      <!-- Bonus Hunt List -->
      <transition name="fade">
        <div v-if="settings.bonus_list == 'hunt'" class="p-6 bg-gray-800 rounded-lg border border-gray-700 mt-4" :key="componentKey">
          <h5 class="text-lg font-bold text-gray-200">Bonus Hunt - List</h5>
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
