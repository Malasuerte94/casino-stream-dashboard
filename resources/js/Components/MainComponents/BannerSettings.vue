<template>
    <div class="p-6">
    <div class="flex justify-between gap-2">
        <input
            type="file"
            id="file" @change="handleFileUpload($event)"
            class="input-primary"
            placeholder="Banner"
        />
        <input
            type="text"
            id="name_banner"
            v-model="banner.name"
            class="input-primary"
            placeholder="Nume banner"
        />
        <button
            @click="uploadBanner"
            type="button"
            tabindex="-1"
            class="justify-center self-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
        >
            Adauga
        </button>
    </div>
    <template v-if="banners">
        <div v-if="!loading" class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="p-6 text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr class="table-row">
                    <th class="p-2 table-cell text-left">ID</th>
                    <th class="p-2 table-cell text-left">Nume</th>
                    <th class="p-2 table-cell text-left">Imagine</th>
                    <th class="p-2 table-cell text-left">Data</th>
                    <th class="p-2 table-cell text-left"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="p-2 bg-white border-b dark:bg-gray-900 dark:border-gray-700" v-for="(banner, index) in banners" :key="banner.id+index">
                    <td class="p-2 table-cell text-left">{{ banner.id }}</td>
                    <td class="p-2 table-cell text-left">{{ banner.name }}</td>
                    <td class="p-2 table-cell text-left"><img class="banner"  :src="banner.image"></td>
                    <td class="p-2 table-cell text-left">{{ formatDateLabels(banner.created_at) }}</td>
                    <td class="p-2 table-cell text-left">
                        <button
                type="button"
                tabindex="-1"
                @click="removeBanner(banner.id)"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
            >
                <svg
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
    </template>
    </div>
</template>
<script >
import axios from "axios";
export default {
    data() {
        return {
            loading: true,
            banner: {
                file: null,
                name: null
            },
            banners: [],
        };
    },
    async mounted() {
        await this.getBanners()
        this.loading = false
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
        async uploadBanner() {
            this.loading = true
            let banner = this.banner
            await axios
                .post("/api/banner", {
                    name: banner.name,
                    image: banner.file
                },{ headers: {
                    'Content-Type': 'multipart/form-data'
                }})
                .then((response) => {
                    this.bonusHuntGames.push(response.data);
                })
                .catch((error) => {
                    console.log(error);
                });
            await this.getBanners()
            this.loading = false
        },
        async removeBanner(id) {
            this.loading = true
            await axios
                .delete("/api/banner/" + id)
                .catch((error) => {
                    console.log(error)
                });
            await this.getBanners()
            this.loading = false
        },
        handleFileUpload(event) {
            this.banner.file = event.target.files[0];
        },
        formatDateLabels(dateRaw) {
            const date = new Date(dateRaw);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is 0-indexed
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
    },
};
</script>
<style>
.banner {
    max-height: 30px;
    width: auto;
}
</style>
