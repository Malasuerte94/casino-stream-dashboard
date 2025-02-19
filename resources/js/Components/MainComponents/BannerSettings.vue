<template>
  <div class="p-6">
    <!-- Existing Banner System -->
    <h2 class="text-xl font-bold mb-4">Bannere</h2>
    <div class="flex flex-wrap gap-2 items-center">
      <input type="file" @change="handleFileUpload('banner', $event)" class="input-primary" />
      <input type="text" v-model="banner.name" class="input-primary" placeholder="Nume banner" />
      <button
          @click="uploadBanner"
          type="button"
          class="text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm px-5 py-2.5"
      >
        Adauga
      </button>
    </div>

    <div v-if="banners.length > 0" class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400">
        <tr>
          <th class="p-2">ID</th>
          <th class="p-2">Nume</th>
          <th class="p-2">Imagine</th>
          <th class="p-2"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(banner, index) in banners" :key="banner.id + index" class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
          <td class="p-2">{{ banner.id }}</td>
          <td class="p-2">{{ banner.name }}</td>
          <td class="p-2">
            <img :src="banner.image" class="banner-image h-[130px] w-auto" alt="Banner Image" />
          </td>
          <td class="p-2">
            <button @click="removeBanner(banner.id)" class="text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm p-2.5">
              🗑️
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- New Banner Ads System -->
    <h2 class="text-xl font-bold mt-8 mb-4">Banner Ads</h2>
    <div class="flex flex-wrap gap-2 items-center">
      <input type="file" @change="handleFileUpload('bannerAd', $event)" class="input-primary" />
      <input type="text" v-model="bannerAd.name" class="input-primary" placeholder="Nume banner ad" />
      <input type="text" v-model="bannerAd.url" class="input-primary" placeholder="URL" />
      <button
          @click="uploadBannerAd"
          type="button"
          class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5"
      >
        Adauga Ad
      </button>
    </div>

    <div v-if="bannerAds.length > 0" class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400">
        <tr>
          <th class="p-2">ID</th>
          <th class="p-2">Nume</th>
          <th class="p-2">Imagine</th>
          <th class="p-2">URL</th>
          <th class="p-2">Clickuri</th>
          <th class="p-2"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(bannerAd, index) in bannerAds" :key="bannerAd.id + index" class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
          <td class="p-2">{{ bannerAd.id }}</td>
          <td class="p-2">{{ bannerAd.name }}</td>
          <td class="p-2">
            <img :src="bannerAd.image" class="banner-image h-[130px] w-auto" alt="Banner Image" />
          </td>
          <td class="p-2">
            <a :href="bannerAd.url" target="_blank" class="text-blue-500 hover:underline">{{ bannerAd.url }}</a>
          </td>
          <td class="p-2 text-center">{{ bannerAd.clicks }}</td>
          <td class="p-2">
            <button @click="removeBannerAd(bannerAd.id)" class="text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm p-2.5">
              🗑️
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      loading: true,
      banner: {
        file: null,
        name: ""
      },
      banners: [],
      bannerAd: {
        file: null,
        name: "",
        url: ""
      },
      bannerAds: [],
    };
  },
  async mounted() {
    await this.getBanners();
    await this.getBannerAds();
    this.loading = false;
  },
  methods: {
    async getBanners() {
      await axios
          .get("/api/banner")
          .then((response) => {
            this.banners = response.data.banners;
          })
          .catch((error) => {
            console.log(error);
          });
    },
    async getBannerAds() {
      await axios
          .get("/api/banner-ads")
          .then((response) => {
            this.bannerAds = response.data.bannerAds;
          })
          .catch((error) => {
            console.log(error);
          });
    },
    async uploadBanner() {
      this.loading = true;
      let formData = new FormData();
      formData.append("name", this.banner.name);
      formData.append("image", this.banner.file);

      await axios
          .post("/api/banner", formData, {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          })
          .then(() => {
            this.getBanners();
          })
          .catch((error) => {
            console.log(error);
          });

      this.loading = false;
    },
    async uploadBannerAd() {
      this.loading = true;
      let formData = new FormData();
      formData.append("name", this.bannerAd.name);
      formData.append("image", this.bannerAd.file);
      formData.append("url", this.bannerAd.url);

      await axios
          .post("/api/banner-ads", formData, {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          })
          .then(() => {
            this.getBannerAds();
          })
          .catch((error) => {
            console.log(error);
          });

      this.loading = false;
    },
    async removeBanner(id) {
      this.loading = true;
      await axios
          .delete(`/api/banner/${id}`)
          .catch((error) => {
            console.log(error);
          });
      await this.getBanners();
      this.loading = false;
    },
    async removeBannerAd(id) {
      this.loading = true;
      await axios
          .delete(`/api/banner-ads/${id}`)
          .catch((error) => {
            console.log(error);
          });
      await this.getBannerAds();
      this.loading = false;
    },
    handleFileUpload(type, event) {
      this[type].file = event.target.files[0];
    }
  }
};
</script>

<style>
.banner-image {
  max-height: 50px;
  width: auto;
}
</style>
