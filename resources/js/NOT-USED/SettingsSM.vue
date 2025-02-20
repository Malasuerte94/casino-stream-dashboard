<template>
    <div>
        <div class="p-6">
            <h5 class="text-lg font-bold">Social Media</h5>
            <div class="grid grid-cols-4 gap-2 mt-4">
                    <div><input v-model="newSocial.sName" type="text" id="sname"
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Social name" /></div>
                    <div><input v-model="newSocial.sSlug" type="text" id="sslug"
                            class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Slug" /></div>
                    <div><input ref="file" v-on:change="onChangeFileUpload" type="file" id="icon"
                            class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Icon" /></div>
                    <button @click="createNewSocial" type="button"
                    class="w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add Social</button>
                </div>
            <template v-if="socialMedia">
                <div v-for="(social, index) in socialMedia" :key="index"
                    class="grid grid-cols-[30px_minmax(200px,_1fr)_minmax(200px,_1fr)_120px_120px_120px] gap-2 mt-4">
                    <div
                        class="w-8 h-8 justify-center self-center text-white bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:focus:ring-blue-800">
                        1
                    </div>
                    <div><input v-model="social.name" type="text" id="name"
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Social name" /></div>
                    <div><input v-model="social.slug" type="text" id="stake"
                            class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Slug" /></div>
                    <div><input v-model="social.icon" type="text" id="price"
                            class="text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Icon" /></div>
                    <button type="button" tabindex="-1" @click="removeSocial(social.id)"
                        class="text-center text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm p-2.5 inline-flex items-center mr-2 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                        Update
                    </button>
                    <button type="button" tabindex="-1" @click="removeSocial(social.id)"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Delete
                    </button>
                </div>
            </template>
        </div>
    </div>
</template>
<script >
import axios from "axios";
export default {
    data() {
        return {
            socialMedia: [],
            newSocial: {
                sName: '',
                sSlug: '',
                file: '',
            },
        }
    },
    mounted() {
        this.getSocialMedia()
    },
    methods: {
        async getSocialMedia() {
            await axios.get('/api/socials')
                .then(response => {
                    this.socialMedia = response.data
                })
                .catch(error => {
                    console.log(error)
                })
        },
        async createNewSocial() {
            await axios.post('/api/socials', this.newSocial)
                .then(response => {
                    this.getSocialMedia()
                })
                .catch(error => {
                    console.log(error)
                })
        },
        async removeSocial(id) {
            await axios.delete('/api/socials/' + id)
                .then(response => {
                    this.getSocialMedia()
                })
                .catch(error => {
                    console.log(error)
                })
        },
        onChangeFileUpload(event) {
            this.newSocial.file = event.target.files[0]; 
        }
    }
}
</script>