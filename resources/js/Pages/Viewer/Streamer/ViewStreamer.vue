<template>
  <AppLayout title="Main User">
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
            <component :is="currentComponent" :streamerId="streamerId"></component>
        </transition>
      </div>
    </div>
  </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from "axios";
import ViewStreamerHunt from "./Components/BonusHunt/ViewStreamerHunt.vue";
import ViewStreamerBonusBattle from "./Components/BonusBattle/ViewStreamerBonusBattle.vue";
import Referrals from "./Components/Refferal/Referrals.vue";
import ViewSocials from "./Components/Social/ViewSocials.vue";

export default {
  components: {ViewStreamerHunt, AppLayout, Referrals, ViewStreamerBonusBattle, ViewSocials},
  data() {
    return {
      streamer: {},
      loading: true,
    };
  },
  props: {
    streamerId: {
      type: Number,
      required: true
    },
    streamerName: {
      type: String,
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
          .get("/api/viewer/get-streamer/"+this.streamerId)
          .then((response) => {
            this.streamer = response.data.streamer
          });
    },
    navigate(section) {
      this.loading = true;
      router.get(`/${this.streamerName}/${section}`, {
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
        referral: 'Referrals',
        social: 'ViewSocials'
      };
      return sections[this.section] || 'ViewStreamerHunt';
    }
  }
}
</script>