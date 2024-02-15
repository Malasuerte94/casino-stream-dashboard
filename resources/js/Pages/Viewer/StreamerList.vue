<template>
  <ViewerDash title="Bonuses">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-5 gap-10 font-bold border-b-orange-600 border-b-2 mb-5 pb-2">
              <div>Nume</div>
              <div>Echipa</div>
            </div>
            <div class="grid grid-cols-5 gap-10 align-middle items-center" v-for="(streamer, index) in streamerData"
                 :key="index">
              <div class="flex align-middle items-center font-black text-2xl"><img class="profile-pic mr-2" :src="streamer.avatar" alt=""/>{{streamer.name}}</div>
              <div class="align-middle items-center text-2xl">{{streamer.team ?? '-'}}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ViewerDash>
</template>
<script>
import ViewerDash from '@/Layouts/ViewerDash.vue';
export default {
  components: { ViewerDash },
  data() {
    return {
      settings: [],
    };
  },
  props: {
    streamers: Array
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
