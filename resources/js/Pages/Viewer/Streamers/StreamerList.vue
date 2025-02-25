<template>
  <AppLayout title="Bonuses">
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-row gap-6">
          <a class="backdrop-blur-xl bg-white/10 shadow-black/40 border border-white/20 overflow-hidden shadow-xl flex flex-col sm:rounded-lg p-6 w-full md:w-1/4 hover:scale-110 transition-all" v-for="(streamer, index) in streamerData"
             :key="index" :href="'/'+streamer.name" >
            <div class="flex flex-col text-center align-middle items-center font-black text-2xl">
              <div class="mb-2">{{streamer.name}}</div>
              <img class="profile-pic" :src="streamer.avatar" alt=""/>
              <div class="align-middle items-center text-sm mt-2">{{streamer.team ?? 'Fără Echipă'}}</div>
            </div>
            <div class="text-center">
              Mai multe detalii curând.
            </div>
          </a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
export default {
  components: { AppLayout },
  data() {
    return {
      settings: [],
    };
  },
  props: {
    streamers: Object
  },
  async mounted() {

  },
  methods: {
  },
  computed: {
    streamerData() {
     return this.streamers['data'];
    }
  }
}
</script>
