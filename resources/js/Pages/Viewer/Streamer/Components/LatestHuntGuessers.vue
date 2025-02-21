<template>
  <div class="relative" v-if="latestHunt && allGuessers.length > 0">
    <div
        class="backdrop-blur-lg bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-4 rounded-lg gap-4 flex flex-col text-white relative"
    >
      <div class="font-semibold">Participanți</div>
      <div class="rounded-lg border border-white/30 overflow-hidden">
        <table v-if="allGuessers.length > 0" class="w-full text-white overflow-hidden">
          <!-- Table Header -->
          <thead class="sticky top-0 backdrop-blur-lg bg-white/30 shadow-lg shadow-black/20 text-left z-10">
          <tr>
            <th class="px-2 py-1 md:px-4 md:py-2">Nume</th>
            <th class="px-2 py-1 md:px-4 md:py-2">Data</th>
          </tr>
          </thead>
        </table>
        <!-- Scrollable Table Body -->
        <div class="max-h-[250px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-700 scrollbar-thumb-rounded-full scrollbar-track-rounded-full">
          <table class="w-full text-white">
            <tbody class="divide-y divide-white/20">
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            <tr v-for="(guesser, index) in allGuessers"
                :key="guesser.id"
                class="shadow-lg shadow-black/40"
                :class="index % 2 === 0 ? 'bg-white/10' : 'bg-white/20'">
              <template v-if="guesser.updated_at">
                <td class="px-2 py-1 md:px-4 md:py-2 flex items-center gap-2">
                  <img :src="guesser.user.profile_photo_path" alt="" class="rounded-full h-8 w-8">
                  {{ guesser.user.name }}
                </td>
                <td class="px-2 py-1 md:px-4 md:py-2 text-left">{{ convertDate(guesser.updated_at) }}</td>
              </template>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  props: {
    latestHunt: {
      type: Object,
      required: true
    },
    listType: {
      type: String,
      required: true
    },
    currency: {
      type: String,
      required: false,
      default: 'RON'
    }
  },
  data() {
    return {
      allGuessers: []
    };
  },
  async mounted() {
    await this.fetchExistingPredictions();
  },
  methods: {
    async fetchExistingPredictions() {
      try {
        const response = await axios.get(`/api/viewer/get-predictions/${this.latestHunt.id}`);
        if (response.data && response.data.predictions) {
          this.allGuessers = response.data.predictions;
        }
      } catch (error) {
        console.error("Error fetching existing predictions:", error);
      }
    },
    convertDate(isoString) {
      const date = new Date(isoString);
      return new Intl.DateTimeFormat("ro-RO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false
      }).format(date);
    },
  }
};
</script>
