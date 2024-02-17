<template>
  <ViewerDash title="Bonuses">
    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="flex lg:flex-row flex-col gap-2">
        <div class="lg:w-1/3 w-full mx-auto sm:px-2 lg:px-2">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <InputLabel for="estimated" value="Introdu valoarea estimată (câștigul total pentru bonus list)" />
              <TextInput
                  id="estimated"
                  v-model="estimated"
                  type="number"
                  class="mt-1 block w-full"
                  required
                  autofocus
              />
              <PrimaryButton @click="addEntry" class="mt-2" :class="{ 'opacity-25': alreadyEstimated }" :disabled="alreadyEstimated || !is_open">
                Patricipă
              </PrimaryButton>
            </div>
          </div>
        </div>
        <div class="lg:w-1/2 w-full mx-auto sm:px-2 lg:px-2">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="mb-2">Despre Lista</div>
              <span class="font-bold text-red-800 mr-4">Cost: {{list.start ?? 0}}</span>
              <span class="font-bold text-green-800 mr-4">Rezultat: {{list.result ?? 0}}</span>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-[10px_1fr_1fr_1fr_1fr] gap-2 mb-2 font-bold border-b-gray-800 border-b-[1px]">
                <span>#</span>
                <span>Nume</span>
                <span>Miză</span>
                <span>Rezultat</span>
                <span>Multi</span>
              </div>
              <div class="grid grid-cols-[10px_1fr_1fr_1fr_1fr] gap-2" v-for="(game, index) in games"
                   :key="'a-' + game.id">
                <span>{{game.id}}</span>
                <span>{{game.name}}</span>
                <span>{{game.stake}}</span>
                <span>{{game.result}}</span>
                <span>{{game.multiplier}}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="lg:w-1/2 w-full mx-auto sm:px-2 lg:px-2">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              Participanți
              <div class="grid grid-cols-[10px_1fr_1fr_1fr_1fr_1fr] gap-2 mb-2 font-bold border-b-gray-800 border-b-[1px]">
                <span>#</span>
                <span>Nume</span>
                <span>Rezultat</span>
                <span>Big X</span>
                <span>Small X</span>
                <span>Game</span>
              </div>
              <div class="grid grid-cols-[10px_1fr_1fr_1fr_1fr_1fr] gap-2" v-for="(entry, index) in entries"
                   :key="'a-' + entry.id">
                <span>{{entry.id}}</span>
                <span class="flex items-center gap-2"><img class="avatar-entry" :src="entry.avatar" alt="">{{entry.user}}</span>
                <span>{{entry.result}}</span>
                <span>{{entry.biggest_multi ?? '-'}}</span>
                <span>{{entry.lowest_multi ?? '-'}}</span>
                <span>{{entry.game_winner ?? '-'}}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <DialogModal :show="show">
      <template #title>
        Notificare
      </template>
      <template #content>
        {{message.data}}
      </template>
      <template #footer>
        <PrimaryButton @click="show = !show">
          Ok
        </PrimaryButton>
      </template>
    </DialogModal>
    <!-- Main modal -->
  </ViewerDash>
</template>
<script>
import ViewerDash from '@/Layouts/ViewerDash.vue';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DialogModal from '@/Components/DialogModal.vue';

import axios from "axios";
export default {
  components: { ViewerDash, TextInput, InputLabel, PrimaryButton, DialogModal },
  data() {
    return {
      message: '',
      show: false,
      estimated: '0',
      alreadyEstimated: false,
      entries: []
    };
  },
  props: {
    list: Object,
    games: Object,
    type: String,
    is_open: Boolean
  },
  async mounted() {
    await this.getEntries();
    this.startRefreshing();
  },
  beforeDestroy() {
    this.stopRefreshing();
  },
  methods: {
    async addEntry() {
      await axios
          .post("/api/add-entry", {
            estimated_result: this.estimated,
            bonus_id: this.list.id,
            bonus_type: this.type
          })
          .then((response) => {
            this.message = response
          })
          .catch((error) => {
            this.message = error
          });
    },
    async getEntries() {
      await axios
          .get("/api/show-entries/"+this.list.id+'/'+this.type)
          .then((response) => {
            this.entries = response.data
          });
    },
    startRefreshing() {
      this.refreshInterval = setInterval(() => {
        this.getEntries();
      }, 10000);
    },
    stopRefreshing() {
      clearInterval(this.refreshInterval);
    },
  },
  computed: {
    bonusList() {
      return this.list['data'];
    }
  },
  watch: {
    message() {
      this.show = true
    }
  }
}
</script>