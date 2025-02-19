<template>
  <ViewerDash title="Main User">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
        <div class="overflow-hidden mb-4">
          <div class="p-6 flex flex-row justify-center items-center gap-4">
            <div>
              <button class="btn-primary-transparent">
                BONUS HUNT
              </button>
            </div>
            <div><img :src="streamer.avatar" alt="Main User" class="rounded-full h-20 w-20 object-cover"></div>
            <div>
              <button class="btn-primary-transparent">
                BONUS BATTLE
              </button>
            </div>
          </div>
        </div>
        <ViewStreamerHunt :steamerId="steamerId" />
      </div>
    </div>
  </ViewerDash>
</template>
<script>
import ViewerDash from '@/Layouts/ViewerDash.vue';
import axios from "axios";
import ViewStreamerHunt from "@/Pages/Viewer/Components/ViewStreamerHunt.vue";

export default {
  components: {ViewStreamerHunt, ViewerDash},
  data() {
    return {
      streamer: {},
    };
  },
  props: {
    steamerId: {
      type: Number,
      required: true
    }
  },
  async mounted() {
    await this.getStreamer();
  },
  methods: {
    async getStreamer() {
      await axios
          .get("/api/viewer/get-streamer/"+this.steamerId)
          .then((response) => {
            this.streamer = response.data.streamer
          });
    },
  }
}
</script>