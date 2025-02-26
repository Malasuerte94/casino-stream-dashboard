<template>
  <div>
    <!-- Top Link & Controls -->
    <div class="px-6 py-3 flex items-center justify-between bg-gray-800 rounded-md shadow-md transition-all duration-300">
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-300">LINK pentru View-eri (predicții)</span>
        <div class="text-sm font-bold py-1 px-3 bg-gray-700 rounded-md border border-gray-600 transition">
          {{ guessUrl }}
        </div>
      </div>
      <div class="flex items-center space-x-4" v-if="!isEnded">
        <!-- Toggle Switch -->
        <span class="ml-3 text-sm font-medium text-gray-300 flex items-center gap-2">
          {{ open_list ? 'Predicții Pornite' : 'Predicții Oprite' }}
          <label for="toggle" class="relative inline-flex items-center cursor-pointer">
            <input
                type="checkbox"
                id="toggle"
                v-model="open_list"
                @change="setStatusGuessList"
                class="sr-only peer"
            />
            <div
                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full
            dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white
            after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border
            after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"
            ></div>
          </label>
        </span>
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
        <div v-if="settings.bonus_list == 'buy'" class="p-6 bg-gray-800 rounded-md border border-gray-700 transition-all duration-300" :key="componentKey">
          <h5 class="text-lg font-bold text-gray-200 mb-2">Bonus Buy - List</h5>
          <BonusBuy @newlist="getUrlGuessList()" />
        </div>
      </transition>
      <!-- Bonus Hunt List -->
      <transition name="fade">
        <div v-if="settings.bonus_list == 'hunt'" class="p-6 bg-gray-800 rounded-md border border-gray-700 transition-all duration-300" :key="componentKey">
          <h5 class="text-lg font-bold text-gray-200 mb-2">Bonus Hunt - List</h5>
          <BonusHunt @newlist="getUrlGuessList()" @update="getUrlGuessList()" />
        </div>
      </transition>
    </div>
  </div>
</template>
<script>
import BonusBuy from "./BonusBuy.vue";
import BonusHunt from "./BonusHunt.vue";
import axios from "axios";

export default {
  components: { BonusBuy, BonusHunt },
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
            this.getUrlGuessList();
          });
    },
    activateDialog() {
      if (this.listCost <= 0 && this.settings.bonus_list == 'hunt') {
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
      return `${host}/${this.$page.props.user.name}`;
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
