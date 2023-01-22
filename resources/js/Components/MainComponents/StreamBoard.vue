<template>
    <div>
        <div class="grid grid-cols-3 gap-2">
            <div>
                <label for="deposit" class="block mb-2 text-sm font-medium text-gray-900">Deposit</label>
                <input v-model="stream.deposit" type="text" id="deposit"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Depus în lei">
            </div>
            <div>
                <label for="casino" class="block mb-2 text-sm font-medium text-gray-900">Casino</label>
                <input v-model="stream.casino" type="text" id="casino"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Nume Casino">
            </div>
            <div class="self-end">
                <button type="button" @click="updateStream" class="w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Update</button>
            </div>
        </div>
    </div>
</template>
<script >
import axios from "axios";
export default {
    data() {
        return {
            stream: [],
        }
    },
    mounted() {
        this.getLatestStream()
    },
    methods: {
        async getLatestStream() {
            await axios.get('/api/stream')
                .then(response => {
                    this.stream = response.data.stream
                })
                .catch(error => {
                    console.log(error)
                })
        },
        async updateStream() {
            await axios.patch('/api/stream/update', {
                id: this.stream.id,
                deposit: this.stream.deposit,
                casino: this.stream.casino,
            })
                .then(response => {
                    this.stream = response.data.stream
                })
                .catch(error => {
                    console.log(error)
                })
        },
        async createNewStream() {
            await axios.post('/api/stream/new')
                .then(response => {
                    this.stream = response.data.stream
                })
                .catch(error => {
                    console.log(error)
                })
            await this.getLatestStream()
        },
    }
}
</script>