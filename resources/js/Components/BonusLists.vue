<template>
  <div>
    <div class="px-6 py-2 flex w-full flex-row items-center justify-between">
      <div class="flex items-center">
      <span class="mr-4">LINK pentru View-eri (ghicește suma)</span>
        <div class="text-sm font-bold py-2 px-4 bg-zinc-500 rounded text-white border-gray-800 border-[2px]">
            {{ guessUrl }}
        </div>
      </div>
      <div class="flex gap-2">
        <label class="inline-flex cursor-pointer btn-primary p-2 w-full items-center justify-center">
          <span class="uppercase mr-2">Înscrieri {{open_list ? ' Pornite' : ' Oprite'}}</span>
          <input type="checkbox" true-value="true" false-value="false" v-model="open_list" @change="setStatusGuessList" class="sr-only peer">
          <div class="relative border-white border-solid border-[1px] w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
        </label>
        <button
            :disabled="isEnded"
            :class="{'input-disabled': isEnded}"
            @click="activateDialog"
            type="button"
            class="w-full btn-secondary"
        >
          ÎNCHIDE LISTA / DECLARĂ CÂȘTIGĂTOR
        </button>
      </div>
    </div>
    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
      <div class="p-6">
        <div class="grid grid-cols-2 gap-2">
          <div
              class="flex items-center pl-4 btn-big btn-secondary"
              :class="{'btn-active': settings.bonus_list === 'buy'}"
          >
            <input
                @change="updateSettings"
                id="bordered-radio-2"
                type="radio"
                value="buy"
                v-model="settings.bonus_list"
                name="bonus-list-display"
                class="w-4 h-4"
            />
            <label
                for="bordered-radio-2"
                class="w-full py-2 ml-2"
            >Afișează Buy</label
            >
          </div>
          <div
              class="flex items-center pl-4 btn-big btn-secondary "
              :class="{'btn-active': settings.bonus_list === 'hunt'}"
          >
            <input
                @change="updateSettings"
                id="bordered-radio-1"
                type="radio"
                value="hunt"
                v-model="settings.bonus_list"
                name="bonus-list-display"
                class="w-4 h-4"
            />
            <label
                for="bordered-radio-1"
                class="w-full py-2 ml-2"
            >Afișează Hunt</label
            >
          </div>
        </div>
      </div>
      <div v-if="settings.bonus_list == 'buy'" class="p-6" :key="componentKey">
        <h5 class="text-lg font-bold">Bonus Buy - List</h5>
        <BonusBuy @newlist="getUrlGuessList()"/>
      </div>

      <div v-if="settings.bonus_list == 'hunt'" class="p-6" :key="componentKey">
        <h5 class="text-lg font-bold">Bonus Hunt - List</h5>
        <BonusHunt @newlist="getUrlGuessList()"/>
      </div>
    </div>
  </div>
</template>
<script>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import BonusBuy from "@/Components/MainComponents/BonusBuy.vue";
import BonusHunt from "@/Components/MainComponents/BonusHunt.vue";
import StreamBoard from "@/Components/MainComponents/StreamBoard.vue";
import {initFlowbite} from "flowbite";

export default {
  components: {ApplicationLogo, BonusBuy, BonusHunt, StreamBoard},
  data() {
    return {
      open_list: false,
      settings: [],
      latestList: '',
      listCost: 0,
      componentKey: 0,
      listEnded: 0
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
        this.$emit('displayOnly', this.settings.bonus_list);
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
            this.latestList = response.data['list-id'];
            this.open_list = response.data['is_open'] === 1;
            this.listCost = response.data.bonusList.start;
          });
    },
    async setStatusGuessList() {
      await axios.post("/api/set-latest-list", {
        is_open: this.open_list,
        list_id: this.latestList,
        type: this.settings.bonus_list
      }).then(() => {
        this.getUrlGuessList()
      });
    },
    async closeBonusList() {
      await axios.post("/api/close-bonus-list", {
        close: true,
        list_id: this.latestList,
        type: this.settings.bonus_list
      }).finally(
          () => {
            this.componentKey++;
          }
      );
    },
    activateDialog() {
      if(this.listCost <= 0) {
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
    }
  },
  watch: {
    settings() {
      this.getUrlGuessList()
    },
  }
};
</script>
