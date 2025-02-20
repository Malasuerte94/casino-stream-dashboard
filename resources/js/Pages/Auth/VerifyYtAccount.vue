<template>
  <ViewerDash title="Bonuses">
    <div class="min-h-screen flex flex-col justify-center items-center ">
      <div class="flex flex-col gap-2 text-center bg-gray-600 text-lg w-2/6 p-4 rounded-lg">
        <div>
          Pentru a verifica contul de YouTube!
        </div>
        <div>
          Scrie aceasta comandă pe chat!
        </div>
        <div class="bg-red-700 p-2 cursor-pointer select-all" @click="copyCommand">
          {{ command }}
        </div>
        <div class="text-sm">
          Click sau Tap pe comandă, se copiază automat!
        </div>
        <div v-if="copied" class="text-green-300 mt-2">
          Comanda copiată!
        </div>
      </div>
    </div>
  </ViewerDash>
</template>

<script>
import ViewerDash from '@/Layouts/ViewerDash.vue';
import axios from 'axios';

export default {
  components: { ViewerDash },
  data() {
    return {
      command: '',
      copied: false
    };
  },
  mounted() {
    axios.get('/api/viewer/get-yt-code')
        .then(response => {
          if (response.data && response.data.code) {
            this.command = '!verific ' + response.data.code;
          }
        })
        .catch(error => {
          console.error("Error fetching verification code:", error);
        });
  },
  methods: {
    copyCommand() {
      navigator.clipboard.writeText(this.command)
          .then(() => {
            this.copied = true;
            // Reset the copied state after 2 seconds
            setTimeout(() => {
              this.copied = false;
            }, 2000);
          })
          .catch(err => {
            console.error("Failed to copy text:", err);
          });
    }
  }
};
</script>