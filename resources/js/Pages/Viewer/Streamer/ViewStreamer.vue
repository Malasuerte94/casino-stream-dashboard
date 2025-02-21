<template>
  <ViewerDash title="Main User">
    <div class="py-6 pb-20">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
        <div class="overflow-hidden mb-4">
          <div class="p-6 flex flex-row justify-center items-center gap-8 relative">
            <!-- Left Buttons Wrapper -->
            <div class="flex flex-row gap-4 flex-1 justify-end">
              <button @click="navigate('hunt')" class="btn-primary-transparent">HUNT</button>
              <button @click="navigate('battle')" class="btn-primary-transparent">BATTLE</button>
            </div>

            <!-- Centered User Logo -->
            <div class="flex-shrink-0">
              <img :src="streamer.avatar" alt="Main User" class="rounded-full h-20 w-20 object-cover">
            </div>

            <!-- Right Buttons Wrapper -->
            <div class="flex flex-row gap-4 flex-1 justify-start">
              <button @click="navigate('referral')" class="btn-primary-transparent">REFERRAL</button>
              <button @click="navigate('social')" class="btn-primary-transparent">SOCIAL</button>
            </div>
          </div>
        </div>
        <!-- Dynamically load the correct component -->
        <transition name="fade" mode="out-in">
            <component :is="currentComponent" :steamerId="steamerId"></component>
        </transition>
      </div>
    </div>
  </ViewerDash>
</template>
<script>
import ViewerDash from '@/Layouts/ViewerDash.vue';
import { router } from '@inertiajs/vue3';
import axios from "axios";
import ViewStreamerHunt from "./Components/ViewStreamerHunt.vue";
import ViewStreamerBonusBattle from "./Components/ViewStreamerBonusBattle.vue";
import Referrals from "./Components/Referrals.vue";

export default {
  components: {ViewStreamerHunt, ViewerDash, Referrals, ViewStreamerBonusBattle},
  data() {
    return {
      streamer: {},
      loading: true,
    };
  },
  props: {
    steamerId: {
      type: Number,
      required: true
    },
    section: {
      type: String,
      required: true
    }
  },
  async mounted() {
    await this.getStreamer();
    this.loading = false;
  },
  methods: {
    async getStreamer() {
      await axios
          .get("/api/viewer/get-streamer/"+this.steamerId)
          .then((response) => {
            this.streamer = response.data.streamer
          });
    },
    navigate(section) {
      this.loading = true;
      router.get(`/streamer/${this.steamerId}/${section}`, {
        onSuccess: () => {
          this.loading = false;
        }
      });
    }
  },
  computed: {
    currentComponent() {
      const sections = {
        hunt: 'ViewStreamerHunt',
        battle: 'ViewStreamerBonusBattle',
        referral: 'Referrals'
      };
      return sections[this.section] || 'ViewStreamerHunt';
    }
  }
}
</script>